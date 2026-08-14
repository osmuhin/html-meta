<?php

namespace Osmuhin\HtmlMeta\Dto;

use Osmuhin\HtmlMeta\Contracts\Dto;

/** Favicon or apple-touch icon entry from a link[rel] tag. */
class Icon implements Dto
{
	/** Absolute or relative icon URL. */
	public string $url;

	/** MIME type from the type attribute or guessed from the extension. */
	public ?string $mime = null;

	public ?string $extension = null;

	/** Parsed from the sizes attribute when present. */
	public int|string|null $width = null;

	/** Parsed from the sizes attribute when present. */
	public int|string|null $height = null;

	public ?string $sizes = null;

	public function toArray(): array
	{
		return [
			'url' => $this->url,
			'mime' => $this->mime,
			'extension' => $this->extension,
			'width' => $this->width,
			'height' => $this->height,
			'sizes' => $this->sizes
		];
	}
}
