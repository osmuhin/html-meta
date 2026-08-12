<?php

namespace Osmuhin\HtmlMeta;

use RuntimeException;

/**
 * @internal Resolves the Container associated with the current Crawler via call stack.
 *
 * Prefer passing Container explicitly when constructing distributors outside the default flow.
 */
class ServiceLocator
{
	private static array $containers = [];

	private static int $count = 0;

	/**
	 * @throws RuntimeException When no container can be resolved for the caller
	 */
	public static function container(): Container
	{
		if (self::$count === 1) {
			return current(current(self::$containers));
		}

		[$class, $objectId] = self::getContainerCredentials(
			debug_backtrace()
		);

		return self::$containers[$class][$objectId];
	}

	/** Register a container for the calling object (typically Crawler). */
	public static function register(Container $container): void
	{
		$trace = debug_backtrace();
		$object = next($trace)['object'];
		$objectId = spl_object_id($object);
		$class = $object::class;

		if (!isset(self::$containers[$class])) {
			self::$containers[$class] = [];
		}

		self::$containers[$class][$objectId] = $container;
		self::$count++;
	}

	/**
	 * Unregister the container for the calling object.
	 *
	 * @throws RuntimeException When no matching container is found
	 */
	public static function destructContainer(): void
	{
		[$class, $objectId] = self::getContainerCredentials(
			debug_backtrace()
		);

		unset(self::$containers[$class][$objectId]);
		self::$count--;

		if (!self::$containers[$class]) {
			unset(self::$containers[$class]);
		}
	}

	/**
	 * @throws \RuntimeException
	 */
	private static function getContainerCredentials(array $trace): array
	{
		while ($item = next($trace)) {
			if (!$object = @$item['object']) {
				continue;
			}

			$class = $object::class;

			if (isset(self::$containers[$class])) {
				$objectId = spl_object_id($object);

				if (isset(self::$containers[$class][$objectId])) {
					return [$class, $objectId];
				}

				throw new RuntimeException("No container associated with object {$objectId} of class {$class}");
			}
		}

		throw new RuntimeException('Unable to find container');
	}
}
