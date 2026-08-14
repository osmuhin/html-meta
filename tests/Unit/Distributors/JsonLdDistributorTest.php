<?php

namespace Tests\Unit\Distributors;

use Osmuhin\HtmlMeta\Distributors\JsonLdDistributor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\ElementCreator;
use Tests\Unit\Traits\SetupContext;

final class JsonLdDistributorTest extends TestCase
{
	use ElementCreator, SetupContext;

	private JsonLdDistributor $distributor;

	protected function setUp(): void
	{
		$this->distributor = new JsonLdDistributor($this->context);
	}

	public static function canHandleProvider(): array
	{
		return [
			['script', ['type' => 'application/ld+json'], true],
			['script', ['type' => 'application/ld+json; charset=utf-8'], true],
			['script', ['type' => 'APPLICATION/LD+JSON'], true],
			['script', ['type' => 'text/javascript'], false],
			['script', [], false],
			['meta', ['type' => 'application/ld+json'], false]
		];
	}

	#[DataProvider('canHandleProvider')]
	public function test_can_handle_method(string $name, array $attributes, bool $expected): void
	{
		$this->distributor->el = self::makeElement($name, $attributes, '{}');

		self::assertSame($expected, $this->distributor->canHandle());
	}

	public function test_handle_parses_single_object_and_resolves_url(): void
	{
		$this->config->processUrlsWith('https://example.com/path/');

		$json = '{"@context":"https://schema.org","@type":"Article","@id":"https://example.com/article#id","inLanguage":"en","url":"/article","headline":"Hello"}';

		$this->distributor->el = self::makeElement('script', ['type' => 'application/ld+json'], $json);
		$this->distributor->handle();

		self::assertCount(1, $this->meta->jsonLd->scripts);
		self::assertTrue($this->meta->jsonLd->scripts[0]->valid);
		self::assertSame($json, $this->meta->jsonLd->scripts[0]->raw);
		self::assertSame('Article', $this->meta->jsonLd->nodes[0]->type);
		self::assertSame('https://example.com/article#id', $this->meta->jsonLd->nodes[0]->id);
		self::assertSame('en', $this->meta->jsonLd->nodes[0]->inLanguage);
		self::assertSame('https://example.com/article', $this->meta->jsonLd->nodes[0]->url);
		self::assertSame('Hello', $this->meta->jsonLd->nodes[0]->data['headline']);
	}

	public function test_handle_flattens_graph_and_top_level_array(): void
	{
		$graph = '{"@context":"https://schema.org","@graph":[{"@type":"WebPage","@id":"#webpage"},{"@type":"Organization","@id":"#org"}]}';
		$array = '[{"@type":"Person","name":"Alice"},{"@type":"Person","name":"Bob"}]';

		$this->distributor->el = self::makeElement('script', ['type' => 'application/ld+json'], $graph);
		$this->distributor->handle();

		$this->distributor->el = self::makeElement('script', ['type' => 'application/ld+json'], $array);
		$this->distributor->handle();

		self::assertCount(2, $this->meta->jsonLd->scripts);
		self::assertSame(['WebPage', 'Organization', 'Person', 'Person'], array_map(
			fn ($node) => $node->type,
			$this->meta->jsonLd->nodes
		));
	}

	public function test_invalid_json_is_stored_without_nodes(): void
	{
		$this->distributor->el = self::makeElement('script', ['type' => 'application/ld+json'], '{not json');
		$this->distributor->handle();

		self::assertCount(1, $this->meta->jsonLd->scripts);
		self::assertFalse($this->meta->jsonLd->scripts[0]->valid);
		self::assertNull($this->meta->jsonLd->scripts[0]->decoded);
		self::assertSame([], $this->meta->jsonLd->nodes);
	}

	public function test_html_comment_wrapped_json_is_decoded(): void
	{
		$json = "<!--\n{\"@type\":\"WebSite\",\"url\":\"https://example.com\"}\n-->";

		$this->distributor->el = self::makeElement('script', ['type' => 'application/ld+json'], $json);
		$this->distributor->handle();

		self::assertTrue($this->meta->jsonLd->scripts[0]->valid);
		self::assertSame('WebSite', $this->meta->jsonLd->nodes[0]->type);
	}
}
