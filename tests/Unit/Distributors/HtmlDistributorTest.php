<?php

namespace Tests\Unit\Distributors;

use Osmuhin\HtmlMeta\Distributors\HtmlDistributor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\ElementCreator;
use Tests\Unit\Traits\SetupContext;

final class HtmlDistributorTest extends TestCase
{
	use ElementCreator, SetupContext;

	private HtmlDistributor $distributor;

	protected function setUp(): void
	{
		$this->distributor = new HtmlDistributor($this->context);
	}

	#[Test]
	#[TestDox('Test "canHandle" method of the distributor')]
	public function test_can_handle_method(): void
	{
		$this->distributor->el = self::makeElement('head');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeElement('html');
		self::assertTrue($this->distributor->canHandle());
	}

	#[Test]
	public function test_handle_method(): void
	{
		$this->distributor->el = self::makeElement('html', ['lang' => 'ru_RU', 'dir' => 'rtl', 'class' => 'dark']);
		$this->distributor->handle();

		self::assertSame('ru_RU', $this->meta->lang);
		self::assertSame('rtl', $this->meta->dir);

		self::assertCount(3, $this->meta->htmlAttributes);

		self::assertArrayHasKey('lang', $this->meta->htmlAttributes);
		self::assertArrayHasKey('dir', $this->meta->htmlAttributes);
		self::assertArrayHasKey('class', $this->meta->htmlAttributes);

		self::assertSame('ru_RU', $this->meta->htmlAttributes['lang']);
		self::assertSame('rtl', $this->meta->htmlAttributes['dir']);
		self::assertSame('dark', $this->meta->htmlAttributes['class']);
	}
}
