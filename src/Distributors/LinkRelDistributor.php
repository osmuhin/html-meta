<?php

namespace Osmuhin\HtmlMeta\Distributors;

use Osmuhin\HtmlMeta\DataMappers\AbstractDataMapper;

/** Handles `<link rel="...">` tags such as canonical. */
class LinkRelDistributor extends AbstractDistributor
{
	public string $rel;

	protected AbstractDataMapper $dataMapper;

	public function __construct()
	{
		parent::__construct();

		$this->dataMapper = new class extends AbstractDataMapper {};
	}

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
			$this->dataMapper->assignPropertyWithObject(
				$this->meta,
				$this->dataMapper->url('canonical'),
				$href
			);
		}
	}
}
