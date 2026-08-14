<?php

namespace Osmuhin\HtmlMeta\Dto;

use Osmuhin\HtmlMeta\Contracts\Dto;
use Osmuhin\HtmlMeta\Dto\JsonLd\Node;
use Osmuhin\HtmlMeta\Dto\JsonLd\Script;

/**
 * JSON-LD documents found in `<script type="application/ld+json">` tags.
 *
 * https://www.w3.org/TR/json-ld/
 * https://schema.org
 */
class JsonLd implements Dto
{
	/** @var \Osmuhin\HtmlMeta\Dto\JsonLd\Script[] */
	public array $scripts = [];

	/** @var \Osmuhin\HtmlMeta\Dto\JsonLd\Node[] */
	public array $nodes = [];

	public function toArray(): array
	{
		return [
			'scripts' => array_map(fn (Script $script) => $script->toArray(), $this->scripts),
			'nodes' => array_map(fn (Node $node) => $node->toArray(), $this->nodes),
		];
	}
}
