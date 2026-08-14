<?php

namespace Osmuhin\HtmlMeta\Distributors;

/** Parent for `<link>` elements; defers work to sub-distributors. */
class LinkDistributor extends AbstractDistributor
{
	public function canHandle(): bool
	{
		return $this->el->name === 'link' && $this->el->attributes;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function handle(): void
	{
		//
	}
}
