<?php

namespace Osmuhin\HtmlMeta\Distributors;

/** Handles the HTML `<title>` element. */
class TitleDistributor extends AbstractDistributor
{
	public function canHandle(): bool
	{
		return $this->el->name === 'title';
	}

	public function handle(): void
	{
		$this->meta->title = $this->el->innerText;
	}
}
