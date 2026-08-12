<?php

namespace Osmuhin\HtmlMeta;

use Osmuhin\HtmlMeta\Dto\Meta;

/**
 * Shared parsing state passed to distributors and data mappers.
 */
final class Context
{
	public function __construct(
		public readonly Meta $meta,
		public readonly Config $config,
	) {
		//
	}
}
