<?php

namespace Tests\Unit;

use Osmuhin\HtmlMeta\Utils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\SetupContainer;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

class UtilsTest extends TestCase
{
	use SetupContainer;

	public static function pathsProvider(): array
	{
		return [
			['/path/to/file.ico', 'ico'],
			['path/to/file.with.dots.png', 'png'],
			['/anotherfilename.nonexistentext', 'nonexistentext'],
			['without-slashes.pdf', 'pdf'],
			['/a.sd.//filewithoutextension', null],
			['filewithoutextension', null]
		];
	}

	public static function urlsForSplittingProvider(): array
	{
		return [
			['https://example1.com/', ['https://example1.com', '']],
			['https://example1.com', ['https://example1.com', '']],
			['http://example4.com/some-path-1/some-path-2/', ['http://example4.com', 'some-path-1/some-path-2']],
			['http://example4.com/some-path-1/some-path-2', ['http://example4.com', 'some-path-1/some-path-2']],
			['//cdn.example.com/path/', ['//cdn.example.com', 'path']],
			['/some-path-1/path/', ['', 'some-path-1/path']],
			['example2.com', ['', 'example2.com']],
		];
	}

	public static function processUrlProvider(): array
	{
		return [
			['https://example.com', '/favicon.ico', 'https://example.com/favicon.ico'],
			['https://example.com', 'favicon.ico', 'https://example.com/favicon.ico'],
			['https://example.com/', 'favicon.ico', 'https://example.com/favicon.ico'],
			['https://example.com/dir', 'favicon.ico', 'https://example.com/favicon.ico'],
			['https://example.com/dir/', 'favicon.ico', 'https://example.com/dir/favicon.ico'],
			['https://example.com/page/', 'https://cdn.example.com', 'https://cdn.example.com'],
			['https://example.com/page/', 'https://cdn.example.com/x.png', 'https://cdn.example.com/x.png'],
			['https://example.com/page/', '//cdn.example.com/x', 'https://cdn.example.com/x'],
			['https://x.com/some-path', 'favicon.ico', 'https://x.com/favicon.ico'],
			['https://x.com/some-path/', 'favicon.ico', 'https://x.com/some-path/favicon.ico'],
			['https://x.com/some-path', '/favicon.ico', 'https://x.com/favicon.ico'],
			['https://x.com/some-path', 'https://apple.com/favicon.ico', 'https://apple.com/favicon.ico'],
		];
	}

	public function test_guess_mime_type_by_file_extension(): void
	{
		assertSame(
			'image/jpeg',
			Utils::guessMimeType('jpeg')
		);

		assertNull(Utils::guessMimeType('nonexistentext'));
	}

	#[DataProvider('pathsProvider')]
	public function test_can_get_file_extension(string $path, ?string $expected): void
	{
		assertSame(
			$expected,
			Utils::guessExtension($path)
		);
	}

	#[DataProvider('urlsForSplittingProvider')]
	public function test_split_url(string $url, array $expected): void
	{
		assertSame(
			$expected,
			Utils::splitUrl($url)
		);
	}

	#[DataProvider('processUrlProvider')]
	public function test_url_processing(string $base, string $input, string $expected): void
	{
		$this->config->processUrlsWith($base);

		assertSame($expected, Utils::processUrl($input));
	}
}
