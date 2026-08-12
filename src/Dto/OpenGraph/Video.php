<?php

namespace Osmuhin\HtmlMeta\Dto\OpenGraph;

use Osmuhin\HtmlMeta\Contracts\Dto;

/** Open Graph video object (`og:video` and related properties). */
class Video implements Dto
{
	public ?string $url = null;

	public ?string $secureUrl = null;

	public ?string $type = null;

	public int|string|null $width = null;

	public int|string|null $height = null;

	public function toArray(): array
	{
		return [
			'url' => $this->url,
			'secureUrl' => $this->secureUrl,
			'type' => $this->type,
			'width' => $this->width,
			'height' => $this->height
		];
	}
}
