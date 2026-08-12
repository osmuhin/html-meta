<?php

namespace Tests\Unit\Distributors;

use Osmuhin\HtmlMeta\DataMappers\MetaDataMapper;
use Osmuhin\HtmlMeta\Distributors\MetaDistributor;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\DataMapperInjector;
use Tests\Unit\Traits\ElementCreator;
use Tests\Unit\Traits\SetupContainer;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class MetaDistributorTest extends TestCase
{
	use ElementCreator, SetupContainer, DataMapperInjector;

	private MetaDistributor $distributor;

	protected function setUp(): void
	{
		$this->distributor = new MetaDistributor();
	}

	public function test_can_handle_method(): void
	{
		$this->distributor->el = self::makeElement('title', innerText: 'Hello world');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeElement('meta');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeElement('meta', ['charset' => 'UTF-8']);
		self::assertTrue($this->distributor->canHandle());
	}

	public function test_handle_method_uses_data_mapper(): void
	{
		$dataMapper = self::createMock(MetaDataMapper::class);

		$dataMapper->expects($this->once())
			->method('assign')
			->with($this->identicalTo('viewport'), $this->identicalTo('user-scalable=no, width=device-width, initial-scale=1.0'))
			->willReturn(true);

		self::injectDataMapper($this->distributor, $dataMapper);

		$this->distributor->el = self::makeNamedMetaElement('viewport', 'user-scalable=no, width=device-width, initial-scale=1.0');
		$this->distributor->handle();
	}

	public function test_can_distributor_handles_charset(): void
	{
		$this->distributor->el = self::makeElement('meta', ['charset' => 'CP1251']);
		$this->distributor->handle();

		assertEquals('cp1251', $this->meta->charset);
	}

	public function test_can_distributor_handles_empty_content(): void
	{
		$dataMapper = self::createMock(MetaDataMapper::class);
		$dataMapper->expects($this->never())->method('assign');
		self::injectDataMapper($this->distributor, $dataMapper);

		$this->distributor->el = self::makeNamedMetaElement('viewport', "  \n   ");
		$this->distributor->handle();

		assertNull($this->meta->viewport);
		assertSame([], $this->meta->unrecognizedMeta);
	}

	public function test_how_distributor_fills_title(): void
	{
		$this->meta->title = 'Test123';

		$this->distributor->el = self::makeNamedMetaElement('title', '123Test');
		$this->distributor->canHandle();
		$this->distributor->handle();

		self::assertSame('Test123', $this->meta->title);

		$this->meta->title = null;

		$this->distributor->el = self::makeNamedMetaElement('title', '12345Test');
		$this->distributor->canHandle();
		$this->distributor->handle();

		self::assertSame('12345Test', $this->meta->title);
	}

	public function test_theme_color(): void
	{
		$this->distributor->el = self::makeNamedMetaElement('theme-color', 'cyan');
		$this->distributor->canHandle();
		$this->distributor->handle();

		self::assertSame([0 => 'cyan'], $this->meta->themeColor);

		$this->distributor->el = self::makeNamedMetaElement('theme-color', '#fff', ['media' => '(prefers-color-scheme: light)']);
		$this->distributor->canHandle();
		$this->distributor->handle();

		self::assertSame([
			0 => 'cyan',
			'(prefers-color-scheme: light)' => '#fff'
		], $this->meta->themeColor);
	}
}
