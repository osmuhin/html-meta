<?php

namespace Tests\Unit\Dto;

use Osmuhin\HtmlMeta\Dto\HttpEquiv;
use Osmuhin\HtmlMeta\Dto\Meta;
use Osmuhin\HtmlMeta\Dto\Twitter;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertSame;

final class DtoToArrayTest extends TestCase
{
	public function test_twitter_to_array_nests_other(): void
	{
		$twitter = new Twitter();
		$twitter->card = 'summary';
		$twitter->other = ['twitter:app:id:iphone' => '123'];

		assertSame([
			'card' => 'summary',
			'site' => null,
			'title' => null,
			'description' => null,
			'image' => null,
			'imageAlt' => null,
			'creator' => null,
			'other' => ['twitter:app:id:iphone' => '123'],
		], $twitter->toArray());
	}

	public function test_http_equiv_to_array_nests_other(): void
	{
		$httpEquiv = new HttpEquiv();
		$httpEquiv->contentType = 'text/html';
		$httpEquiv->other = ['non-standard' => 'value'];

		$array = $httpEquiv->toArray();

		assertSame('text/html', $array['contentType']);
		assertSame(['non-standard' => 'value'], $array['other']);
		assertArrayHasKey('refresh', $array);
	}

	public function test_meta_to_array_serializes_nested_dtos_and_unrecognized(): void
	{
		$meta = new Meta();
		$meta->title = 'T';
		$meta->unrecognizedMeta = ['fb:app_id' => '1'];
		$meta->twitter->card = 'summary';

		$array = $meta->toArray();

		assertSame('T', $array['title']);
		assertIsArray($array['twitter']);
		assertSame('summary', $array['twitter']['card']);
		assertArrayHasKey('other', $array['twitter']);
		assertIsArray($array['favicon']);
		assertIsArray($array['openGraph']);
		assertIsArray($array['httpEquiv']);
		assertSame(['fb:app_id' => '1'], $array['unrecognizedMeta']);
	}
}
