<?php

namespace Osmuhin\HtmlMeta\Contracts;

/** Data transfer object that can be exported as an array. */
interface Dto
{
	/** @return array<string, mixed> */
	public function toArray(): array;
}
