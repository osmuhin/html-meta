<?php

namespace Tests\Unit\Distributors;

use Osmuhin\HtmlMeta\Distributors\TitleDistributor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\ElementCreator;
use Tests\Unit\Traits\SetupContext;

final class TitleDistributorTest extends TestCase
{
	use ElementCreator, SetupContext;

	private TitleDistributor $distributor;

	protected function setUp(): void
	{
		$this->distributor = new TitleDistributor($this->context);
	}

	#[Test]
	#[TestDox('Test "canHandle" method of the distributor')]
	public function test_can_handle_method(): void
	{
		$this->distributor->el = self::makeElement('h1', innerText: 'Hello world');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeElement('title', innerText: 'Hello world');
		self::assertTrue($this->distributor->canHandle());
	}

	#[Test]
	#[TestDox('Test "handle" method of the distributor')]
	public function test_handle_method(): void
	{
		$this->distributor->el = self::makeElement('title', innerText: '123Hello world321');
		$this->distributor->handle();

		self::assertSame('123Hello world321', $this->meta->title);
	}
}
