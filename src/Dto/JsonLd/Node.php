<?php

namespace Osmuhin\HtmlMeta\Dto\JsonLd;

use Osmuhin\HtmlMeta\Contracts\Dto;

/** Flattened JSON-LD entity (`@type`, `@id`, `inLanguage`, `url`). */
class Node implements Dto
{
	public string|array|null $type = null;

	public ?string $id = null;

	public ?string $inLanguage = null;

	public ?string $url = null;

	public array $data = [];

	public function toArray(): array
	{
		return [
			'type' => $this->type,
			'id' => $this->id,
			'inLanguage' => $this->inLanguage,
			'url' => $this->url,
			'data' => $this->data,
		];
	}
}
