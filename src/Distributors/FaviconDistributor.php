<?php

namespace Osmuhin\HtmlMeta\Distributors;

use Osmuhin\HtmlMeta\DataMappers\BasicDataMapper;
use Osmuhin\HtmlMeta\Dto\Icon;
use Osmuhin\HtmlMeta\Utils;

/** Handles favicon, apple-touch-icon, and web manifest link tags. */
class FaviconDistributor extends AbstractDistributor
{
	protected ?string $rel;

	protected string $href;

	public function canHandle(): bool
	{
		switch ($this->rel = $this->elAttr('rel')) {
			case 'shortcut icon':
			case 'icon':
			case 'apple-touch-icon':
			case 'manifest':
				return true;
		}

		return false;
	}

	public function handle(): void
	{
		if (!$this->href = $this->elAttr('href', lowercase: false)) {
			return;
		}

		$mapper = $this->dataMapper(BasicDataMapper::class);

		switch ($this->rel) {
			case 'shortcut icon':
			case 'icon':
				$this->meta->favicon->icons[] = $this->makeIcon($mapper);
				break;
			case 'apple-touch-icon':
				$this->meta->favicon->appleTouchIcons[] = $this->makeIcon($mapper);
				break;
			case 'manifest':
				$mapper->assignPropertyWithObject(
					$this->meta->favicon,
					$mapper->url('manifest'),
					$this->href
				);
				break;
		}
	}

	protected function makeIcon(BasicDataMapper $mapper): Icon
	{
		$icon = new Icon();
		$icon->extension = Utils::guessExtension($this->href);

		$mapper->assignPropertyWithObject(
			$icon,
			$mapper->url('url'),
			$this->href
		);

		if ($icon->sizes = $this->elAttr('sizes')) {
			$explodedSizes = explode('x', $icon->sizes);

			if (\count($explodedSizes) === 2) {
				$mapper->assignPropertyWithObject(
					$icon,
					$mapper->int('width'),
					$explodedSizes[0]
				);

				$mapper->assignPropertyWithObject(
					$icon,
					$mapper->int('height'),
					$explodedSizes[1]
				);
			}
		}

		if (!$icon->mime = $this->elAttr('type')) {
			$icon->mime = Utils::guessMimeType($icon->extension);
		}

		return $icon;
	}
}
