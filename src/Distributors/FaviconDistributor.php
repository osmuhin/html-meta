<?php

namespace Osmuhin\HtmlMeta\Distributors;

use Osmuhin\HtmlMeta\DataMappers\AbstractDataMapper;
use Osmuhin\HtmlMeta\Dto\Icon;
use Osmuhin\HtmlMeta\Utils;

/** Handles favicon, apple-touch-icon, and web manifest link tags. */
class FaviconDistributor extends AbstractDistributor
{
	protected ?string $rel;

	protected string $href;

	protected AbstractDataMapper $dataMapper;

	public function __construct()
	{
		parent::__construct();

		$this->dataMapper = new class extends AbstractDataMapper {};
	}

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

		switch ($this->rel) {
			case 'shortcut icon':
			case 'icon':
				$this->meta->favicon->icons[] = $this->makeIcon();
				break;
			case 'apple-touch-icon':
				$this->meta->favicon->appleTouchIcons[] = $this->makeIcon();
				break;
			case 'manifest':
				$this->dataMapper->assignPropertyWithObject(
					$this->meta->favicon,
					$this->dataMapper->url('manifest'),
					$this->href
				);
				break;
		}
	}

	protected function makeIcon(): Icon
	{
		$icon = new Icon();
		$icon->extension = Utils::guessExtension($this->href);

		$this->dataMapper->assignPropertyWithObject(
			$icon,
			$this->dataMapper->url('url'),
			$this->href
		);

		if ($icon->sizes = $this->elAttr('sizes')) {
			$explodedSizes = explode('x', $icon->sizes);

			if (\count($explodedSizes) === 2) {
				$this->dataMapper->assignPropertyWithObject(
					$icon,
					$this->dataMapper->int('width'),
					$explodedSizes[0]
				);

				$this->dataMapper->assignPropertyWithObject(
					$icon,
					$this->dataMapper->int('height'),
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
