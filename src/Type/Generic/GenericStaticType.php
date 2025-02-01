<?php declare(strict_types = 1);

namespace PHPStan\Type\Generic;

use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProviderStaticAccessor;
use PHPStan\Type\CompoundType;
use PHPStan\Type\ErrorType;
use PHPStan\Type\IsSuperTypeOfResult;
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\TypeWithClassName;
use function array_key_exists;
use function array_map;
use function count;

/** @api */
class GenericStaticType extends StaticType
{

	private ?ObjectType $staticObjectType = null;

	private string $baseClass;

	/**
	 * @api
	 * @param array<int, Type> $types
	 * @param array<int, TemplateTypeVariance> $variances
	 */
	public function __construct(
		private ClassReflection $classReflection,
		private array $types,
		private ?Type $subtractedType,
		private array $variances,
	)
	{
		parent::__construct($classReflection, $subtractedType);
		$this->baseClass = $classReflection->getName();
	}

	public function getClassName(): string
	{
		return $this->baseClass;
	}

	/**
	 * @return array<int, Type>
	 */
	public function getTypes(): array
	{
		return $this->types;
	}

	/** @return array<int, TemplateTypeVariance> */
	public function getVariances(): array
	{
		return $this->variances;
	}

	public function getStaticObjectType(): ObjectType
	{
		if ($this->staticObjectType === null) {
			if ($this->classReflection->isGeneric()) {
				return $this->staticObjectType = new GenericObjectType(
					$this->classReflection->getName(),
					$this->types,
					$this->subtractedType,
					$this->classReflection,
					$this->variances,
				);
			}

			return $this->staticObjectType = parent::getStaticObjectType();
		}

		return $this->staticObjectType;
	}

	public function changeBaseClass(ClassReflection $classReflection): StaticType
	{
		if ($classReflection->getName() === $this->getClassName()) {
			return $this;
		}

		// this template type mapping logic is very similar to mapping logic in MutatingScope::exactInstantiation()
		// where inferring "new Foo" but with the constructor being only in Foo parent class

		$newType = new GenericObjectType($classReflection->getName(), $classReflection->typeMapToList($classReflection->getTemplateTypeMap()));
		$ancestorType = $newType->getAncestorWithClassName($this->getClassName());
		if ($ancestorType === null) {
			return new self($classReflection, $classReflection->typeMapToList($classReflection->getTemplateTypeMap()->resolveToBounds()), $this->subtractedType, $this->variances);
		}

		$ancestorClassReflections = $ancestorType->getObjectClassReflections();
		if (count($ancestorClassReflections) !== 1) {
			return new self($classReflection, $classReflection->typeMapToList($classReflection->getTemplateTypeMap()->resolveToBounds()), $this->subtractedType, $this->variances);
		}

		$ancestorClassReflection = $ancestorClassReflections[0];
		$ancestorMapping = [];
		foreach ($ancestorClassReflection->getActiveTemplateTypeMap()->getTypes() as $typeName => $templateType) {
			if (!$templateType instanceof TemplateType) {
				continue;
			}

			$ancestorMapping[$typeName] = $templateType;
		}

		$resolvedTypeMap = [];
		foreach ($ancestorClassReflection->typeMapFromList($this->types)->getTypes() as $typeName => $type) {
			if (!array_key_exists($typeName, $ancestorMapping)) {
				continue;
			}

			$ancestorType = $ancestorMapping[$typeName];
			if (!$ancestorType->getBound()->isSuperTypeOf($type)->yes()) {
				continue;
			}

			if (!array_key_exists($ancestorType->getName(), $resolvedTypeMap)) {
				$resolvedTypeMap[$ancestorType->getName()] = $type;
				continue;
			}

			$resolvedTypeMap[$ancestorType->getName()] = TypeCombinator::union($resolvedTypeMap[$ancestorType->getName()], $type);
		}

		$resolvedVariances = [];
		foreach ($ancestorClassReflection->varianceMapFromList($this->variances)->getVariances() as $typeName => $variance) {
			if (!array_key_exists($typeName, $ancestorMapping)) {
				continue;
			}

			$ancestorType = $ancestorMapping[$typeName];
			if (!array_key_exists($ancestorType->getName(), $resolvedVariances)) {
				$resolvedVariances[$ancestorType->getName()] = $variance;
				continue;
			}

			$resolvedVariances[$ancestorType->getName()] = $resolvedVariances[$ancestorType->getName()]->compose($variance);
		}

		return new self($classReflection, $classReflection->typeMapToList(new TemplateTypeMap($resolvedTypeMap)), $this->subtractedType, $classReflection->varianceMapToList(new TemplateTypeVarianceMap($resolvedVariances)));
	}

	public function isSuperTypeOfWithReason(Type $type): IsSuperTypeOfResult
	{
		if ($type instanceof CompoundType) {
			return $type->isSubTypeOfWithReason($this);
		}

		if ($type instanceof self) {
			return $this->getStaticObjectType()->isSuperTypeOfWithReason($type->getStaticObjectType());
		}

		return parent::isSuperTypeOfWithReason($type)->and(IsSuperTypeOfResult::createMaybe());
	}

	public function traverse(callable $cb): Type
	{
		$subtractedType = $this->getSubtractedType() !== null ? $cb($this->getSubtractedType()) : null;

		$typesChanged = false;
		$types = [];
		foreach ($this->types as $type) {
			$newType = $cb($type);
			$types[] = $newType;
			if ($newType === $type) {
				continue;
			}

			$typesChanged = true;
		}

		if ($subtractedType !== $this->getSubtractedType() || $typesChanged) {
			return new self(
				$this->classReflection,
				$types,
				$subtractedType,
				$this->variances,
			);
		}

		return $this;
	}

	public function traverseSimultaneously(Type $right, callable $cb): Type
	{
		if (!$right instanceof TypeWithClassName) {
			return $this;
		}

		$ancestor = $right->getAncestorWithClassName($this->getClassName());
		if (!$ancestor instanceof self) {
			return $this;
		}

		if (count($this->types) !== count($ancestor->types)) {
			return $this;
		}

		$typesChanged = false;
		$types = [];
		foreach ($this->types as $i => $leftType) {
			$rightType = $ancestor->types[$i];
			$newType = $cb($leftType, $rightType);
			$types[] = $newType;
			if ($newType === $leftType) {
				continue;
			}

			$typesChanged = true;
		}

		if ($typesChanged) {
			return new self(
				$this->classReflection,
				$types,
				null,
				$this->variances,
			);
		}

		return $this;
	}

	public function changeSubtractedType(?Type $subtractedType): Type
	{
		if ($subtractedType !== null) {
			$classReflection = $this->getClassReflection();
			if ($classReflection->getAllowedSubTypes() !== null) {
				$objectType = $this->getStaticObjectType()->changeSubtractedType($subtractedType);
				if ($objectType instanceof NeverType) {
					return $objectType;
				}

				if ($objectType instanceof ObjectType && $objectType->getSubtractedType() !== null) {
					return new self($classReflection, $this->types, $objectType->getSubtractedType(), $this->variances);
				}

				return TypeCombinator::intersect($this, $objectType);
			}
		}

		return new self(
			$this->classReflection,
			$this->types,
			$subtractedType,
			$this->variances,
		);
	}

	public function inferTemplateTypes(Type $receivedType): TemplateTypeMap
	{
		return $this->getStaticObjectType()->inferTemplateTypes($receivedType);
	}

	public function getReferencedTemplateTypes(TemplateTypeVariance $positionVariance): array
	{
		return $this->getStaticObjectType()->getReferencedTemplateTypes($positionVariance);
	}

	public function toPhpDocNode(): TypeNode
	{
		/** @var IdentifierTypeNode $parent */
		$parent = parent::toPhpDocNode();
		return new GenericTypeNode(
			$parent,
			array_map(static fn (Type $type) => $type->toPhpDocNode(), $this->types),
			array_map(static fn (TemplateTypeVariance $variance) => $variance->toPhpDocNodeVariance(), $this->variances),
		);
	}

	/**
	 * @param mixed[] $properties
	 */
	public static function __set_state(array $properties): Type
	{
		$reflectionProvider = ReflectionProviderStaticAccessor::getInstance();
		if ($reflectionProvider->hasClass($properties['baseClass'])) {
			return new self(
				$reflectionProvider->getClass($properties['baseClass']),
				$properties['types'],
				$properties['subtractedType'],
				$properties['variances'],
			);
		}

		return new ErrorType();
	}

}
