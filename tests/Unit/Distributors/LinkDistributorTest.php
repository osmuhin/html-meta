<?php

namespace Tests\Unit\Distributors;

use Osmuhin\HtmlMeta\Distributors\LinkDistributor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\ElementCreator;
use Tests\Unit\Traits\SetupContext;

final class LinkDistributorTest extends TestCase
{
	use ElementCreator, SetupContext;

	private LinkDistributor $distributor;

	protected function setUp(): void
	{
		$this->distributor = new LinkDistributor($this->context);
	}

	#[Test]
	#[TestDox('Test "canHandle" method of the distributor')]
	public function test_can_handle_method(): void
	{
		$this->distributor->el = self::makeElement('h1', innerText: 'Hello world');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeElement('link');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeElement('link', ['some-attribute' => '']);
		self::assertTrue($this->distributor->canHandle());
	}
}
