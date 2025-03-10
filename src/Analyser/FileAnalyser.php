<?php declare(strict_types = 1);

namespace PHPStan\Analyser;

use PhpParser\Comment;
use PhpParser\Node;
use PHPStan\BetterReflection\NodeCompiler\Exception\UnableToCompileNode;
use PHPStan\BetterReflection\Reflection\Exception\NotAClassReflection;
use PHPStan\BetterReflection\Reflection\Exception\NotAnInterfaceReflection;
use PHPStan\BetterReflection\Reflector\Exception\IdentifierNotFound;
use PHPStan\Dependency\DependencyResolver;
use PHPStan\Node\FileNode;
use PHPStan\Parser\Parser;
use PHPStan\Rules\FileRuleError;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\LineRuleError;
use PHPStan\Rules\MetadataRuleError;
use PHPStan\Rules\NonIgnorableRuleError;
use PHPStan\Rules\Registry;
use PHPStan\Rules\TipRuleError;
use function array_key_exists;
use function array_unique;

class FileAnalyser
{

	private \PHPStan\Analyser\ScopeFactory $scopeFactory;

	private \PHPStan\Analyser\NodeScopeResolver $nodeScopeResolver;

	private \PHPStan\Parser\Parser $parser;

	private DependencyResolver $dependencyResolver;

	private bool $reportUnmatchedIgnoredErrors;

	public function __construct(
		ScopeFactory $scopeFactory,
		NodeScopeResolver $nodeScopeResolver,
		Parser $parser,
		DependencyResolver $dependencyResolver,
		bool $reportUnmatchedIgnoredErrors
	)
	{
		$this->scopeFactory = $scopeFactory;
		$this->nodeScopeResolver = $nodeScopeResolver;
		$this->parser = $parser;
		$this->dependencyResolver = $dependencyResolver;
		$this->reportUnmatchedIgnoredErrors = $reportUnmatchedIgnoredErrors;
	}

	/**
	 * @param string $file
	 * @param array<string, true> $analysedFiles
	 * @param Registry $registry
	 * @param callable(\PhpParser\Node $node, Scope $scope): void|null $outerNodeCallback
	 * @return FileAnalyserResult
	 */
	public function analyseFile(
		string $file,
		array $analysedFiles,
		Registry $registry,
		?callable $outerNodeCallback
	): FileAnalyserResult
	{
		$fileErrors = [];
		$fileDependencies = [];
		$exportedNodes = [];
		if (is_file($file)) {
			try {
				$parserNodes = $this->parser->parseFile($file);
				$linesToIgnore = [];
				$temporaryFileErrors = [];
				$nodeCallback = function (\PhpParser\Node $node, Scope $scope) use (&$fileErrors, &$fileDependencies, &$exportedNodes, $file, $registry, $outerNodeCallback, $analysedFiles, &$linesToIgnore, &$temporaryFileErrors): void {
					if ($outerNodeCallback !== null) {
						$outerNodeCallback($node, $scope);
					}
					$uniquedAnalysedCodeExceptionMessages = [];
					$nodeType = get_class($node);
					foreach ($registry->getRules($nodeType) as $rule) {
						// We need to identify unique rules to assign an issue code
						$ruleName = get_class($rule);

						try {
							$ruleErrors = $rule->processNode($node, $scope);
						} catch (\PHPStan\AnalysedCodeException $e) {
							if (isset($uniquedAnalysedCodeExceptionMessages[$e->getMessage()])) {
								continue;
							}

							$uniquedAnalysedCodeExceptionMessages[$e->getMessage()] = true;
							$fileErrors[] = new Error($e->getMessage(), $file, $node->getLine(), $e, null, null, $e->getTip());
							continue;
						} catch (IdentifierNotFound $e) {
							$fileErrors[] = new Error(sprintf('Reflection error: %s not found.', $e->getIdentifier()->getName()), $file, $node->getLine(), $e, null, null, 'Learn more at https://phpstan.org/user-guide/discovering-symbols');
							continue;
						} catch (UnableToCompileNode | NotAClassReflection | NotAnInterfaceReflection $e) {
							$fileErrors[] = new Error(sprintf('Reflection error: %s', $e->getMessage()), $file, $node->getLine(), $e);
							continue;
						}

						foreach ($ruleErrors as $ruleError) {
							$nodeLine = $node->getLine();
							$line = $nodeLine;
							$canBeIgnored = true;
							$fileName = $scope->getFileDescription();
							$filePath = $scope->getFile();
							$traitFilePath = null;
							$tip = null;
							$identifier = null;
							$metadata = [];
							if ($scope->isInTrait()) {
								$traitReflection = $scope->getTraitReflection();
								if ($traitReflection->getFileName() !== null) {
									$traitFilePath = $traitReflection->getFileName();
								}
							}
							if (is_string($ruleError)) {
								$message = $ruleError;
							} else {
								$message = $ruleError->getMessage();
								if (
									$ruleError instanceof LineRuleError
									&& $ruleError->getLine() !== -1
								) {
									$line = $ruleError->getLine();
								}
								if (
									$ruleError instanceof FileRuleError
									&& $ruleError->getFile() !== ''
								) {
									$fileName = $ruleError->getFile();
									$filePath = $ruleError->getFile();
									$traitFilePath = null;
								}

								if ($ruleError instanceof TipRuleError) {
									$tip = $ruleError->getTip();
								}

								if ($ruleError instanceof IdentifierRuleError) {
									$identifier = $ruleError->getIdentifier();
								}

								if ($ruleError instanceof MetadataRuleError) {
									$metadata = $ruleError->getMetadata();
								}

								if ($ruleError instanceof NonIgnorableRuleError) {
									$canBeIgnored = false;
								}
							}

							$message = "{$ruleName}:::{$message}";

							$temporaryFileErrors[] = new Error(
								$message,
								$fileName,
								$line,
								$canBeIgnored,
								$filePath,
								$traitFilePath,
								$tip,
								$nodeLine,
								$nodeType,
								$identifier,
								$metadata
							);
						}
					}

					foreach ($this->getLinesToIgnore($node) as $lineToIgnore) {
						$linesToIgnore[$scope->getFileDescription()][$lineToIgnore] = true;
					}

					try {
						$dependencies = $this->dependencyResolver->resolveDependencies($node, $scope);
						foreach ($dependencies->getFileDependencies($scope->getFile(), $analysedFiles) as $dependentFile) {
							$fileDependencies[] = $dependentFile;
						}
						if ($dependencies->getExportedNode() !== null) {
							$exportedNodes[] = $dependencies->getExportedNode();
						}
					} catch (\PHPStan\AnalysedCodeException $e) {
						// pass
					} catch (IdentifierNotFound $e) {
						// pass
					} catch (UnableToCompileNode | NotAClassReflection | NotAnInterfaceReflection $e) {
						// pass
					}
				};

				$scope = $this->scopeFactory->create(ScopeContext::create($file));
				$nodeCallback(new FileNode($parserNodes), $scope);
				$this->nodeScopeResolver->processNodes(
					$parserNodes,
					$scope,
					$nodeCallback
				);
				$unmatchedLineIgnores = $linesToIgnore;
				foreach ($temporaryFileErrors as $tmpFileError) {
					$line = $tmpFileError->getLine();
					if (
						$line !== null
						&& $tmpFileError->canBeIgnored()
						&& array_key_exists($tmpFileError->getFile(), $linesToIgnore)
						&& array_key_exists($line, $linesToIgnore[$tmpFileError->getFile()])
					) {
						unset($unmatchedLineIgnores[$tmpFileError->getFile()][$line]);
						continue;
					}

					$fileErrors[] = $tmpFileError;
				}

				if ($this->reportUnmatchedIgnoredErrors) {
					foreach ($unmatchedLineIgnores as $ignoredFile => $lines) {
						if ($ignoredFile !== $file) {
							continue;
						}

						foreach (array_keys($lines) as $line) {
							$fileErrors[] = new Error(
								sprintf('No error to ignore is reported on line %d.', $line),
								$scope->getFileDescription(),
								$line,
								false,
								$scope->getFile(),
								null,
								null,
								null,
								null,
								'ignoredError.unmatchedOnLine'
							);
						}
					}
				}
			} catch (\PhpParser\Error $e) {
				$fileErrors[] = new Error($e->getMessage(), $file, $e->getStartLine() !== -1 ? $e->getStartLine() : null, $e);
			} catch (\PHPStan\Parser\ParserErrorsException $e) {
				foreach ($e->getErrors() as $error) {
					$fileErrors[] = new Error($error->getMessage(), $e->getParsedFile() ?? $file, $error->getStartLine() !== -1 ? $error->getStartLine() : null, $e);
				}
			} catch (\PHPStan\AnalysedCodeException $e) {
				$fileErrors[] = new Error($e->getMessage(), $file, null, $e, null, null, $e->getTip());
			} catch (IdentifierNotFound $e) {
				$fileErrors[] = new Error(sprintf('Reflection error: %s not found.', $e->getIdentifier()->getName()), $file, null, $e, null, null, 'Learn more at https://phpstan.org/user-guide/discovering-symbols');
			} catch (UnableToCompileNode | NotAClassReflection | NotAnInterfaceReflection $e) {
				$fileErrors[] = new Error(sprintf('Reflection error: %s', $e->getMessage()), $file, null, $e);
			}
		} elseif (is_dir($file)) {
			$fileErrors[] = new Error(sprintf('File %s is a directory.', $file), $file, null, false);
		} else {
			$fileErrors[] = new Error(sprintf('File %s does not exist.', $file), $file, null, false);
		}

		return new FileAnalyserResult($fileErrors, array_values(array_unique($fileDependencies)), $exportedNodes);
	}

	/**
	 * @param Node $node
	 * @return int[]
	 */
	private function getLinesToIgnore(Node $node): array
	{
		$lines = [];
		if ($node->getDocComment() !== null) {
			$line = $this->findLineToIgnoreComment($node->getDocComment());
			if ($line !== null) {
				$lines[] = $line;
			}
		}

		foreach ($node->getComments() as $comment) {
			$line = $this->findLineToIgnoreComment($comment);
			if ($line === null) {
				continue;
			}

			$lines[] = $line;
		}

		return $lines;
	}

	private function findLineToIgnoreComment(Comment $comment): ?int
	{
		$text = $comment->getText();
		if ($comment instanceof Comment\Doc) {
			$line = $comment->getEndLine();
		} else {
			if (strpos($text, "\n") === false || strpos($text, '//') === 0) {
				$line = $comment->getStartLine();
			} else {
				$line = $comment->getEndLine();
			}
		}
		if (strpos($text, '@phpstan-ignore-next-line') !== false) {
			return $line + 1;
		}

		if (strpos($text, '@phpstan-ignore-line') !== false) {
			return $line;
		}

		return null;
	}

}
