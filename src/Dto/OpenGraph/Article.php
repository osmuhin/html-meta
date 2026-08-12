<?php

namespace Osmuhin\HtmlMeta\Dto\OpenGraph;

use Osmuhin\HtmlMeta\Contracts\Dto;

/** Open Graph article object (`article:*` properties). */
class Article implements Dto
{
	public ?string $publishedTime = null;

	public ?string $modifiedTime = null;

	public ?string $expirationTime = null;

	public ?string $section = null;

	/** @var string[] */
	public array $authors = [];

	/** @var string[] */
	public array $tags = [];

	/** @var array<string, string> */
	public array $other = [];

	public function toArray(): array
	{
		return [
			'publishedTime' => $this->publishedTime,
			'modifiedTime' => $this->modifiedTime,
			'expirationTime' => $this->expirationTime,
			'section' => $this->section,
			'authors' => $this->authors,
			'tags' => $this->tags,
			'other' => $this->other,
		];
	}
}
