<?php

namespace Tests\Unit\Distributors;

use Osmuhin\HtmlMeta\Distributors\LinkRelDistributor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\ElementCreator;
use Tests\Unit\Traits\SetupContext;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class LinkRelDistributorTest extends TestCase
{
	use ElementCreator, SetupContext;

	private LinkRelDistributor $distributor;

	protected function setUp(): void
	{
		$this->distributor = new LinkRelDistributor($this->context);
	}

	#[Test]
	#[TestDox('Test "canHandle" method of the distributor')]
	public function test_can_handle_method(): void
	{
		$this->distributor->el = self::makeElement('link', ['rel' => " \t  \n"]);
		assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeElement('link', ['rel' => 'icon']);
		assertTrue($this->distributor->canHandle());
	}

	public function test_canonical(): void
	{
		$this->distributor->el = self::makeElement('link', ['rel' => 'caNonicAl   ', 'href' => "\n\n /catalog/123   "]);

		$this->distributor->canHandle();
		$this->distributor->handle();

		assertSame('/catalog/123', $this->meta->canonical);
	}
}
