<?php

namespace Osmuhin\HtmlMeta\Dto;

use Osmuhin\HtmlMeta\Contracts\Dto;

class Twitter implements Dto
{
	/**
	 * Card type.
	 * It can be "summary", "summary_large_image", "app" or "player".
	 * Example: <meta name="twitter:card" content="summary"\>
	 */
	public ?string $card = null;

	/**
	 * The username of the site's Twitter account.
	 * Example: <meta name="twitter:site" content="@username"\>
	 */
	public ?string $site = null;

	/**
	 * Title of the page content.
	 * Example: <meta name="twitter:title" content="Laravel - The PHP Framework For Web Artisans"\>
	 */
	public ?string $title = null;

	/**
	 * Summary of the page content.
	 * Example: <meta name="twitter:description" content="Laravel is a PHP web application framework with expressive, elegant syntax."\>
	 */
	public ?string $description = null;

	/**
	 * URL link to image of the twitter card.
	 * Example: <meta name="twitter:image" content="https://example.com/image.jpg"\>
	 */
	public ?string $image = null;

	/**
	 * Alternative text for the image for accessibility.
	 * Example: <meta name="twitter:image:alt" content="A description of the image"\>
	 */
	public ?string $imageAlt = null;

	/**
	 * Specifies the account of the content author.
	 * Example: <meta name="twitter:creator" content="@author"\>
	 */
	public ?string $creator = null;

	/**
	 * Any other non-standardized/extended twitter tags.
	 * Example:
	 * \<meta name="twitter:app:id:iphone" content="123456789">
	 * will be saved as ['twitter:app:id:iphone' => '123456789']
	 */
	public array $other = [];

	public function toArray(): array
	{
		return [
			'card' => $this->card,
			'site' => $this->site,
			'title' => $this->title,
			'description' => $this->description,
			'image' => $this->image,
			'imageAlt' => $this->imageAlt,
			'creator' => $this->creator,
			'other' => $this->other
		];
	}
}
