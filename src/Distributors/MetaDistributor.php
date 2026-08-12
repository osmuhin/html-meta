<?php

namespace Osmuhin\HtmlMeta\Distributors;

use Osmuhin\HtmlMeta\DataMappers\MetaDataMapper;

/** Handles `<meta>` tags and delegates http-equiv, Twitter, and Open Graph to sub-distributors. */
class MetaDistributor extends AbstractDistributor
{
	public function canHandle(): bool
	{
		return $this->el->name === 'meta' && $this->el->attributes;
	}

	public function handle(): void
	{
		if ($charset = $this->elAttr('charset')) {
			$this->meta->charset = $charset;

			return;
		}

		if ($name = $this->elAttr('name')) {
			$this->handleNamedMeta($name);

			return;
		}

		if ($property = $this->elAttr('property')) {
			$this->meta->unrecognizedMeta[$property] = $this->elAttr('content', lowercase: false);
		}
	}

	protected function handleNamedMeta(string $name): void
	{
		if (!$content = $this->elAttr('content', lowercase: false)) {
			return;
		}

		if ($this->dataMapper(MetaDataMapper::class)->assign($name, $content)) {
			return;
		}

		switch ($name) {
			case 'title':
				$this->meta->title ??= $content;

				return;
			case 'theme-color':
				if ($media = $this->elAttr('media', lowercase: false)) {
					$this->meta->themeColor[$media] = $content;
				} else {
					$this->meta->themeColor[] = $content;
				}

				return;
		}

		$this->meta->unrecognizedMeta[$name] = $content;
	}
}
