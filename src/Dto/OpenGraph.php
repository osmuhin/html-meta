<?php

namespace Osmuhin\HtmlMeta\Dto;

use Osmuhin\HtmlMeta\Contracts\Dto;
use Osmuhin\HtmlMeta\Dto\OpenGraph\Article;
use Osmuhin\HtmlMeta\Dto\OpenGraph\Audio;
use Osmuhin\HtmlMeta\Dto\OpenGraph\Book;
use Osmuhin\HtmlMeta\Dto\OpenGraph\Image;
use Osmuhin\HtmlMeta\Dto\OpenGraph\Profile;
use Osmuhin\HtmlMeta\Dto\OpenGraph\Video;

/**
 * Open Graph is an Internet protocol that was created by Facebook to standardize the use of
 * meta-data representing the content of a web page.
 *
 * This class contains all the standard properties described in the documentation.
 *
 * https://ogp.me
 */
class OpenGraph implements Dto
{
	public ?string $title = null;

	public ?string $type = null;

	public ?string $url = null;

	public ?string $description = null;

	public ?string $determiner = null;

	public ?string $siteName = null;

	public ?string $locale = null;

	/** @var string[] */
	public array $alternateLocales = [];

	/** @var \Osmuhin\HtmlMeta\Dto\OpenGraph\Image[] */
	public array $images = [];

	/** @var \Osmuhin\HtmlMeta\Dto\OpenGraph\Video[] */
	public array $videos = [];

	/** @var \Osmuhin\HtmlMeta\Dto\OpenGraph\Audio[] */
	public array $audio = [];

	public Article $article;

	public Book $book;

	public Profile $profile;

	/** @var array<string, string> */
	public array $other = [];

	public function __construct()
	{
		$this->article = new Article();
		$this->book = new Book();
		$this->profile = new Profile();
	}

	public function toArray(): array
	{
		return [
			'title' => $this->title,
			'type' => $this->type,
			'url' => $this->url,
			'description' => $this->description,
			'determiner' => $this->determiner,
			'siteName' => $this->siteName,
			'locale' => $this->locale,
			'alternateLocales' => $this->alternateLocales,
			'images' => array_map(fn (Image $image) => $image->toArray(), $this->images),
			'videos' => array_map(fn (Video $video) => $video->toArray(), $this->videos),
			'audio' => array_map(fn (Audio $audio) => $audio->toArray(), $this->audio),
			'article' => $this->article->toArray(),
			'book' => $this->book->toArray(),
			'profile' => $this->profile->toArray(),
			'other' => $this->other
		];
	}
}
