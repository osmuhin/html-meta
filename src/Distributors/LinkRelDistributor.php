<?php

namespace Osmuhin\HtmlMeta\Distributors;

use Osmuhin\HtmlMeta\DataMappers\BasicDataMapper;

/** Handles `<link rel="...">` tags such as canonical. */
class LinkRelDistributor extends AbstractDistributor
{
	public string $rel;

	public function canHandle(): bool
	{
		return (bool) $this->rel = $this->elAttr('rel');
	}

	public function handle(): void
	{
		if (!$href = $this->elAttr('href', lowercase: false)) {
			return;
		}

		if ($this->rel === 'canonical') {
			$mapper = $this->dataMapper(BasicDataMapper::class);
			$mapper->assignPropertyWithObject(
				$this->meta,
				$mapper->url('canonical'),
				$href
			);
		}
	}
}
