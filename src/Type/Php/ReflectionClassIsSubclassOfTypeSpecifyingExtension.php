<?php declare(strict_types = 1);

namespace PHPStan\Type\Php;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\MethodTypeSpecifyingExtension;
use PHPStan\Type\ObjectWithoutClassType;
use ReflectionClass;

final class ReflectionClassIsSubclassOfTypeSpecifyingExtension implements MethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{

	private TypeSpecifier $typeSpecifier;

	public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
	{
		$this->typeSpecifier = $typeSpecifier;
	}

	public function getClass(): string
	{
		return ReflectionClass::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection, MethodCall $node, TypeSpecifierContext $context): bool
	{
		return $methodReflection->getName() === 'isSubclassOf'
			&& isset($node->getArgs()[0])
			&& !$context->null();
	}

	public function specifyTypes(MethodReflection $methodReflection, MethodCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
	{
		$calledOnType = $scope->getType($node->var);
		$reflectionType = $calledOnType->getTemplateType(ReflectionClass::class, 'T');
		if (!(new ObjectWithoutClassType())->isSuperTypeOf($reflectionType)->yes()) {
			return new SpecifiedTypes();
		}

		$valueType = $scope->getType($node->getArgs()[0]->value);
		$objectType = $valueType->getClassStringObjectType();
		$narrowingType = new GenericObjectType(ReflectionClass::class, [$objectType]);

		if (!$reflectionType->isSuperTypeOf($objectType)->yes()) {
			// cause "always false" error
			return $this->typeSpecifier->create(
				$node->var,
				$narrowingType,
				$context,
				$scope,
			);
		}

		if ($objectType->isSuperTypeOf($reflectionType)->yes()) {
			// cause "always true" error
			return $this->typeSpecifier->create(
				$node->var,
				$narrowingType,
				$context,
				$scope,
			);
		}

		return $this->typeSpecifier->create(
			$node->var,
			$narrowingType,
			$context,
			$scope,
		)->setAlwaysOverwriteTypes();
	}

}
