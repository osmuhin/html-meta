<?php

namespace Osmuhin\HtmlMeta\Contracts;

/**
 * Validates an HTML element and writes matching data into Meta DTOs.
 */
interface Distributor
{
	public static function init(): self;

	/**
	 * @param \Osmuhin\HtmlMeta\Contracts\Distributor|string ...$args
	 */
	public function useSubDistributors(...$args): self;

	public function setSubDistributor(self|string $distributor, ?string $insteadOf = null): self;

	public function getSubDistributor(string $class): self|null;

	/**
	 * Whether this distributor can handle the current element.
	 * When true, sub-distributors are polled, then {@see handle()} is called if none claimed the element further.
	 */
	public function canHandle(): bool;

	/** Distribute the current element's data into DTOs. */
	public function handle(): void;
}
