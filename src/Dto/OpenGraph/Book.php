<?php

namespace Osmuhin\HtmlMeta\Dto\OpenGraph;

use Osmuhin\HtmlMeta\Contracts\Dto;

/** Open Graph book object (`book:*` properties). */
class Book implements Dto
{
	/** @var string[] */
	public array $authors = [];

	public ?string $isbn = null;

	public ?string $releaseDate = null;

	/** @var string[] */
	public array $tags = [];

	/** @var array<string, string> */
	public array $other = [];

	public function toArray(): array
	{
		return [
			'authors' => $this->authors,
			'isbn' => $this->isbn,
			'releaseDate' => $this->releaseDate,
			'tags' => $this->tags,
			'other' => $this->other
		];
	}
}
