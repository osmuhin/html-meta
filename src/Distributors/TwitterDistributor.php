<?php

namespace Osmuhin\HtmlMeta\Distributors;

use Osmuhin\HtmlMeta\DataMappers\TwitterDataMapper;

/** Handles Twitter Card `name="twitter:*"` (or property) meta tags. */
class TwitterDistributor extends AbstractDistributor
{
	protected string $name;

	protected string $content;

	protected TwitterDataMapper $dataMapper;

	public function __construct()
	{
		parent::__construct();

		$this->dataMapper = new TwitterDataMapper();
	}

	public function canHandle(): bool
	{
		$name = $this->elAttr('name') ?: $this->elAttr('property');

		if (!$name || !str_starts_with($name, 'twitter:')) {
			return false;
		}

		if (!$content = $this->elAttr('content', lowercase: false)) {
			return false;
		}

		$this->name = $name;
		$this->content = $content;

		return true;
	}

	public function handle(): void
	{
		if ($this->dataMapper->assign($this->name, $this->content)) {
			return;
		}

		$this->meta->twitter->other[$this->name] = $this->content;
	}
}
