<?php

namespace Tests\Feature;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Osmuhin\HtmlMeta\Crawler;
use Osmuhin\HtmlMeta\Distributors\TitleDistributor;
use Osmuhin\HtmlMeta\Dto\Meta;
use Osmuhin\HtmlMeta\Element;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Fixtures\CustomTitleDistributor;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

class HtmlMetaCrawlerTest extends TestCase
{
	public function test_fetching_via_url(): void
	{
		if ($_ENV['SKIP_GUZZLE_TESTS']) {
			$this->markTestSkipped('Guzzle tests are disabled');

			return;
		}

		$meta = Crawler::init(url: 'https://github.com')->run();

		assertInstanceOf(Meta::class, $meta);
	}

	public function test_fetching_via_guzzle_request(): void
	{
		if ($_ENV['SKIP_GUZZLE_TESTS']) {
			$this->markTestSkipped('Guzzle tests are disabled');

			return;
		}

		$request = new GuzzleRequest('GET', 'https://github.com');
		$meta = Crawler::init(request: $request)->run();

		assertInstanceOf(Meta::class, $meta);
	}

	public function test_parsing_base_meta(): void
	{
		$html = file_get_contents(__DIR__ . '/resources/meta.html');

		$meta = Crawler::init(html: $html, url: 'http://example.com/relative/')->run();

		assertSame('utf-8', $meta->charset);
		assertSame('light dark', $meta->colorScheme);
		assertSame('ExampleApp', $meta->applicationName);
		assertSame('Evil Inc.', $meta->copyright);
		assertSame('Some description', $meta->description);
		assertSame('WordPress.com', $meta->generator);
		assertSame('en', $meta->lang);
		assertSame('origin', $meta->referrer);
		assertSame([
			0 => '#eb0c0c',
			'(prefers-color-scheme: dark)' => '#ff0'
		], $meta->themeColor);
		assertSame('Document', $meta->title);
		assertSame('width=device-width, initial-scale=1.0', $meta->viewport);
		assertSame('money, exchange', $meta->keywords);
		assertSame('noindex', $meta->robots);
		assertSame('yes', $meta->appleMobileWebAppCapable);
		assertSame('black', $meta->appleMobileWebAppStatusBarStyle);
		assertSame('telephone=no', $meta->formatDetection);
		assertSame('Osmuhin', $meta->author);
		assertSame('rtl', $meta->dir);
		assertSame('app-id=myAppStoreID, app-argument=myURL', $meta->appleItunesApp);
		assertSame([
			'lang' => 'en',
			'data-theme' => 'dark',
			'dir' => 'rtl'
		], $meta->htmlAttributes);
		assertSame('http://example.com/relative/catalog/123123', $meta->canonical);
	}

	public function test_parsing_http_equiv(): void
	{
		$html = file_get_contents(__DIR__ . '/resources/http-equiv.html');

		$meta = Crawler::init(html: $html)->run();

		assertSame('text/html; charset=UTF-8', $meta->httpEquiv->contentType);
		assertSame('IE=edge', $meta->httpEquiv->xUaCompatible);
		assertSame('no-cache', $meta->httpEquiv->cacheControl);
		assertSame('en', $meta->httpEquiv->contentLanguage);
		assertSame('no-cache', $meta->httpEquiv->pragma);
		assertSame('Tue, January 01, 2025, 12:00:00 GMT', $meta->httpEquiv->expires);
		assertSame('5; url=https://example.com', $meta->httpEquiv->refresh);
		assertSame('default-src \'self\';', $meta->httpEquiv->contentSecurityPolicy);
		assertSame('on', $meta->httpEquiv->xDnsPrefetchControl);
		assertSame('*', $meta->httpEquiv->accessControlAllowOrigin);
		assertSame([
			'non-standart-meta' => 'some-value 2',
			'non-standart-meta-3' => 'some-value 3'
		], $meta->httpEquiv->other);
	}

	public function test_parsing_favicon(): void
	{
		$html = file_get_contents(__DIR__ . '/resources/favicon.html');

		$meta = Crawler::init(html: $html, url: 'http://example.com/path/')->run();

		assertSame([
			'manifest' => 'http://example.com/manifest.webmanifest',
			'icons' => [
				[
					'url' => 'http://example.com/path/favicon-small.ico',
					'mime' => 'application/ico',
					'extension' => 'ico',
					'width' => 16,
					'height' => 14,
					'sizes' => '16x14'
				],
				[
					'url' => 'http://example.com/favicon.ico',
					'mime' => 'application/ico',
					'extension' => 'ico',
					'width' => null,
					'height' => null,
					'sizes' => 'any'
				],
				[
					'url' => 'http://example.com/icon.xsvg',
					'mime' => 'image/svg+xml',
					'extension' => 'xsvg',
					'width' => null,
					'height' => null,
					'sizes' => null
				]
			],
			'appleTouchIcons' => [
				[
					'url' => 'http://example.com/path/apple.com/apple-touch-icon.png',
					'mime' => 'image/png',
					'extension' => 'png',
					'width' => null,
					'height' => null,
					'sizes' => null
				]
			],
		], $meta->favicon->toArray());
	}

	public function test_parsing_twitter(): void
	{
		$html = file_get_contents(__DIR__ . '/resources/twitter.html');

		$crawler = Crawler::init(html: $html, url: 'http://example.com/path');
		$crawler->config->dontProcessUrls();

		$meta = $crawler->run();

		assertSame('summary', $meta->twitter->card);
		assertSame('@username', $meta->twitter->site);
		assertSame('Laravel - The PHP Framework For Web Artisans', $meta->twitter->title);
		assertSame('Some description for twitter card.', $meta->twitter->description);
		assertSame('/image.jpg', $meta->twitter->image);
		assertSame('A description of the image', $meta->twitter->imageAlt);
		assertSame('@author', $meta->twitter->creator);
		assertSame([
			'twitter:app:id:iphone' => '123456789'
		], $meta->twitter->other);
	}

	public function test_parsing_opengraph(): void
	{
		$html = file_get_contents(__DIR__ . '/resources/opengraph.html');

		$crawler = Crawler::init(html: $html, url: 'http://yandex.ru/path/');

		$meta = $crawler->run();

		assertSame([
			'title' => 'Page title',
			'type' => 'website',
			'url' => 'http://yandex.ru/path/products',
			'description' => 'Description of the page that will be displayed in the posts.',
			'determiner' => 'a',
			'siteName' => 'Site name',
			'locale' => 'ru_RU',
			'alternateLocales' => ['en_US'],
			'images' => [
				[
					'url' => 'http://yandex.ru/image.jpg',
					'secureUrl' => null,
					'type' => 'image/jpeg',
					'width' => 1200,
					'height' => 630,
					'alt' => 'Image description',
				]
			],
			'videos' => [
				[
					'url' => 'https://example.com/video.mp4',
					'secureUrl' => 'https://example.com/video.mp4',
					'type' => 'video/mp4',
					'width' => 1280,
					'height' => 720
				]
			],
			'audio' => [
				[
					'url' => 'https://example.com/audio.mp3',
					'secureUrl' => 'https://example.com/audio.mp3',
					'type' => 'audio/mpeg'
				]
			],
			'article' => [
				'publishedTime' => '2026-08-12T11:00:00+03:00',
				'modifiedTime' => null,
				'expirationTime' => null,
				'section' => 'Tech',
				'authors' => ['https://example.com/authors/alice'],
				'tags' => ['php'],
				'other' => []
			],
			'book' => [
				'authors' => [],
				'isbn' => '9780306406157',
				'releaseDate' => null,
				'tags' => [],
				'other' => []
			],
			'profile' => [
				'firstName' => 'Alice',
				'lastName' => 'Example',
				'username' => 'alice',
				'gender' => 'female',
				'other' => []
			],
			'other' => []
		], $meta->openGraph->toArray());

		assertSame([
			'place:location:latitude' => '59.9343',
			'place:location:longitude' => '30.3351',
			'fb:app_id' => '123456789012345',
		], $meta->unrecognizedMeta);
	}

	public function test_replacing_one_distributor_by_anon_another(): void
	{
		$html = '<html><head><title>Google</title></head></html>';

		$crawler = Crawler::init(html: $html);

		$customDistributor = new class($crawler->context) extends TitleDistributor
		{
			public function handle(): void
			{
				$this->meta->title = 'Prefix for title ' . $this->el->innerText;
			}
		};

		$crawler->distributor->setSubDistributor($customDistributor, TitleDistributor::class);

		$meta = $crawler->run();
		assertSame('Prefix for title Google', $meta->title);
	}

	public function test_replacing_one_distributor_by_another(): void
	{
		$html = '<html><head><title>Google</title></head></html>';

		$crawler = Crawler::init(html: $html);

		$crawler->distributor->setSubDistributor(CustomTitleDistributor::class, TitleDistributor::class);

		$meta = $crawler->run();
		assertSame('Google title suffix', $meta->title);
	}
}
