<?php

namespace Osmuhin\HtmlMeta;

use InvalidArgumentException;

/**
 * Minimal service container for binding Config, Meta, and related instances per crawler.
 */
class Container
{
	private array $bindings = [];

	private array $instances = [];

	/** Register a concrete instance or factory callable under `$key`. */
	public function bind(string $key, callable|object $value): void
	{
		$this->bindings[$key] = $value;
	}

	/**
	 * Resolve a binding, caching the result for subsequent calls.
	 *
	 * @throws InvalidArgumentException When no binding exists for `$key`
	 */
	public function get(string $key)
	{
		if (isset($this->instances[$key])) {
			return $this->instances[$key];
		}

		if (!isset($this->bindings[$key])) {
			throw new InvalidArgumentException("No binding found for key \"{$key}\"");
		}

		$binding = $this->bindings[$key];

		return $this->instances[$key] = is_callable($binding) ? $binding($this) : $binding;
	}
}
