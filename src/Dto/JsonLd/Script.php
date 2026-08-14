<?php

namespace Osmuhin\HtmlMeta\Dto\JsonLd;

use Osmuhin\HtmlMeta\Contracts\Dto;

/** One `<script type="application/ld+json">` payload. */
class Script implements Dto
{
	public string $raw = '';

	public mixed $decoded = null;

	public bool $valid = false;

	public function toArray(): array
	{
		return [
			'raw' => $this->raw,
			'decoded' => $this->decoded,
			'valid' => $this->valid
		];
	}
}
