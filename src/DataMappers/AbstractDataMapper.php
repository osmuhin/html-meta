<?php

namespace Osmuhin\HtmlMeta\DataMappers;

use Osmuhin\HtmlMeta\Config;
use Osmuhin\HtmlMeta\Context;
use Osmuhin\HtmlMeta\Contracts\DataMapper;
use Osmuhin\HtmlMeta\Dto\Meta;
use Osmuhin\HtmlMeta\Utils;

/**
 * Shared mapping helpers for assigning attribute values onto DTO properties.
 */
abstract class AbstractDataMapper implements DataMapper
{
	protected Meta $meta;

	protected Config $config;

	public function __construct(Context $context)
	{
		$this->meta = $context->meta;
		$this->config = $context->config;
	}

	public function assignAccordingToTheMap(array $map, object $object, string $name, string $content): bool
	{
		if (isset($map[$name])) {
			$this->assignPropertyWithObject($object, $map[$name], $content);

			return true;
		}

		return false;
	}

	public function assignPropertyWithObject(object $object, string|callable $property, mixed $value): void
	{
		if (is_callable($property)) {
			$property($value, $object);
		} else {
			$object->{$property} ??= $value;
		}
	}

	public function int(string|callable $property): callable
	{
		return function (string $value, object $object) use ($property) {
			if ($this->config->shouldUseTypeConversion()) {
				$value = ctype_digit($value) ? (int) $value : null;
			}

			$this->assignPropertyWithObject($object, $property, $value);
		};
	}

	public function url(string|callable $property): callable
	{
		return function (string $value, object $object) use ($property) {
			if ($this->config->shouldProcessUrls()) {
				$value = Utils::processUrl($value, $this->config);
			}

			$this->assignPropertyWithObject($object, $property, $value);
		};
	}

	public function forceOverwrite(string $property): callable
	{
		return function ($value, $object) use ($property) {
			$object->{$property} = $value;
		};
	}

	public function guessMimeType(string|callable $property, string|callable $propertyType = 'type'): callable
	{
		return function ($value, $object) use ($property, $propertyType) {
			$this->assignPropertyWithObject($object, $property, $value);

			if ($maybeExtension = Utils::guessExtension($value)) {
				$this->assignPropertyWithObject($object, $propertyType, Utils::guessMimeType($maybeExtension));
			}
		};
	}
}
