<?php declare(strict_types = 1);

namespace Bug12073;

trait HasFieldBuildersTrait
{
	/**
	 * @param array<string,mixed> $field
	 */
	abstract public function field(array $field): static;
}

class GroupBuilder
{
	use HasFieldBuildersTrait;

	/** @var array<string,mixed> */
	private array $group = [];

	private function __construct()
	{
	}

	/**
	 * @param array<string,mixed> $field
	 */
	public function field(array $field): static
	{
		if (! is_array($this->group['fields'] ?? null)) {
			$this->group['fields'] = [];
		}

		$this->group['fields'][] = $field;

		return $this;
	}
}
