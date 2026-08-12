<?php

namespace Osmuhin\HtmlMeta\Contracts;

use Osmuhin\HtmlMeta\Context;

/**
 * Validates an HTML element and writes matching data into Meta DTOs.
 */
interface Distributor
{
	public static function init(Context $context): self;

	/**
	 * @param \Osmuhin\HtmlMeta\Contracts\Distributor|string ...$args
	 */
	public function useSubDistributors(...$args): self;

	public function setSubDistributor(self|string $distributor, ?string $insteadOf = null): self;

	public function getSubDistributor(string $class): self|null;

	public function canHandle(): bool;

	public function handle(): void;
}
