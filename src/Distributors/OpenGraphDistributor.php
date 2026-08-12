<?php

namespace Osmuhin\HtmlMeta\Distributors;

use Osmuhin\HtmlMeta\DataMappers\OpenGraphDataMapper;

/** Handles Open Graph `property="og:*"` meta tags. */
class OpenGraphDistributor extends AbstractDistributor
{
	protected string $handlerMethod;

	protected string $property;

	protected string $content;

	protected string $namespace;

	protected OpenGraphDataMapper $dataMapper;

	public function __construct()
	{
		parent::__construct();

		$this->dataMapper = new OpenGraphDataMapper();
	}

	protected static function getMethodsMap(): array
	{
		return [
			'og' => 'setOg',
			// 'image' => 'setImage',
			// 'music' => 'setMusic',
			// 'video' => 'setVideo',
			// 'article' => 'setArticle',
			// 'book' => 'setBook',
			// 'profile' => 'setProfile'
		];
	}

	public function canHandle(): bool
	{
		$property = $this->elAttr('property');
		$content = $this->elAttr('content', lowercase: false);

		if (!$property || !$content || !str_contains($property, ':')) {
			return false;
		}

		$namespace = explode(':', $property, 2)[0];
		$map = self::getMethodsMap();

		if (isset($map[$namespace])) {
			$this->property = $property;
			$this->content = $content;
			$this->namespace = $namespace;
			$this->handlerMethod = $map[$namespace];

			return true;
		}

		return false;
	}

	public function handle(): void
	{
		$this->{$this->handlerMethod}($this->property, $this->content);
	}

	protected function setOg(string $property, string $content): void
	{
		if ($this->dataMapper->assign($property, $content)) {
			return;
		}

		if (str_starts_with($property, 'og:image')) {
			$this->dataMapper->assignImage(
				key: $property,
				content: $content,
				asNew: str_ends_with($property, 'image') || str_ends_with($property, 'image:url')
			);

			return;
		}

		if ($property === 'og:locale:alternate') {
			$this->meta->openGraph->alternateLocales[] = $content;

			return;
		}

		if (str_starts_with($property, 'og:video')) {
			$this->dataMapper->assignVideo(
				key: $property,
				content: $content,
				asNew: str_ends_with($property, 'video') || str_ends_with($property, 'video:url')
			);

			return;
		}

		if (str_starts_with($property, 'og:audio')) {
			$this->dataMapper->assignAudio(
				key: $property,
				content: $content,
				asNew: str_ends_with($property, 'audio') || str_ends_with($property, 'audio:url')
			);

			return;
		}

		$this->meta->unrecognizedMeta[$property] = $content;
	}
}
