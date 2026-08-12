<?php

namespace Tests\Unit\Distributors;

use Osmuhin\HtmlMeta\Distributors\FaviconDistributor;
use Osmuhin\HtmlMeta\Dto\Icon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\ElementCreator;
use Tests\Unit\Traits\SetupContext;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class FaviconDistributorTest extends TestCase
{
	use ElementCreator, SetupContext;

	private FaviconDistributor $distributor;

	protected function setUp(): void
	{
		$this->distributor = new FaviconDistributor($this->context);
	}

	public static function metaAttributesProvider(): array
	{
		return [
			[[], false],
			[['rel' => '', 'href' => ''], false],
			[['rel' => '12312', 'href' => "  \n "], false],
			[['rel' => '   ', 'href' => '123123'], false],
			[['rel' => '  icon  ', 'href' => " \tfavicon.ico"], true]
		];
	}

	public static function iconTypesProvider(): array
	{
		return [
			['icon', 'icons'],
			['shortcut icon', 'icons'],
			['apple-touch-icon', 'appleTouchIcons']
		];
	}

	#[DataProvider('metaAttributesProvider')]
	public function test_can_handle_method(array $attributes, bool $expected): void
	{
		$this->distributor->el = self::makeElement('meta', $attributes);
		assertSame($expected, $this->distributor->canHandle());
	}

	#[DataProvider('iconTypesProvider')]
	public function test_icons_1(string $iconType, string $dtoProperty): void
	{
		$this->distributor->el = self::makeElement('meta', [
			'rel' => $iconType,
			'href' => '/favicon.ico',
			'sizes' => '16X14'
		]);
		$this->distributor->canHandle();
		$this->distributor->handle();

		assertCount(1, $this->meta->favicon->{$dtoProperty});
		assertInstanceOf(Icon::class, $icon = $this->meta->favicon->{$dtoProperty}[0]);
		assertSame('ico', $icon->extension);
		assertSame('application/ico', $icon->mime);
		assertSame('/favicon.ico', $icon->url);
		assertSame('16x14', $icon->sizes);
		assertSame(16, $icon->width);
		assertSame(14, $icon->height);
	}

	#[DataProvider('iconTypesProvider')]
	public function test_icons_2(string $iconType, string $dtoProperty): void
	{
		$this->distributor->el = self::makeElement('meta', [
			'rel' => " \n $iconType ",
			'href' => "\t /favicon.ico  ",
			'type' => '   application/ico123123'
		]);
		$this->distributor->canHandle();
		$this->distributor->handle();

		assertInstanceOf(Icon::class, $icon = $this->meta->favicon->{$dtoProperty}[0]);
		assertSame('application/ico123123', $icon->mime);
		assertNull($icon->sizes);
		assertNull($icon->width);
		assertNull($icon->height);
	}

	public function test_manifest(): void
	{
		$this->distributor->el = self::makeElement('meta', [
			'rel' => '   manifest  ',
			'href' => "\n\n/favicon/manifest.json  "
		]);
		$this->distributor->canHandle();
		$this->distributor->handle();

		assertSame('/favicon/manifest.json', $this->meta->favicon->manifest);
	}
}
