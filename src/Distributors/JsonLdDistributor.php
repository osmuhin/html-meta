<?php

namespace Osmuhin\HtmlMeta\Distributors;

use Osmuhin\HtmlMeta\Dto\JsonLd\Node;
use Osmuhin\HtmlMeta\Dto\JsonLd\Script;
use Osmuhin\HtmlMeta\Utils;

/** Handles `<script type="application/ld+json">` tags. */
class JsonLdDistributor extends AbstractDistributor
{
	public function canHandle(): bool
	{
		if ($this->el->name !== 'script') {
			return false;
		}

		$type = $this->elAttr('type');

		return $type !== null && str_starts_with($type, 'application/ld+json');
	}

	public function handle(): void
	{
		$script = new Script();
		$script->raw = $this->el->innerText ?? '';

		$payload = $this->unwrap($script->raw);
		$decoded = json_decode($payload, true);
		$script->valid = json_last_error() === JSON_ERROR_NONE && is_array($decoded);
		$script->decoded = $script->valid ? $decoded : null;

		$this->meta->jsonLd->scripts[] = $script;

		if (!$script->valid) {
			return;
		}

		foreach ($this->extractNodes($decoded) as $node) {
			$this->meta->jsonLd->nodes[] = $node;
		}
	}

	/**
	 * Strip a surrounding HTML comment so `json_decode` can read the payload.
	 *
	 * Some CMS/SEO plugins emit JSON-LD as:
	 *
	 *     <script type="application/ld+json">
	 *     <!--
	 *     {"@type":"WebSite"}
	 *     -->
	 *     </script>
	 *
	 * Inside `<script>` this is not an HTML comment: the parser treats it as
	 * raw text. `<!--` is a leftover from old JavaScript hiding (it is only a
	 * single-line JS comment there) and does not stop execution by itself —
	 * `type="application/ld+json"` does. Google and several generators still
	 * wrap payloads this way, so the markers must be removed before decoding.
	 */
	private function unwrap(string $raw): string
	{
		$raw = trim($raw);

		if (preg_match('/^<!--(.*)-->$/s', $raw, $matches)) {
			return trim($matches[1]);
		}

		return $raw;
	}

	/** @return Node[] */
	private function extractNodes(mixed $value): array
	{
		if (!is_array($value)) {
			return [];
		}

		if (array_is_list($value)) {
			$nodes = [];

			foreach ($value as $item) {
				array_push($nodes, ...$this->extractNodes($item));
			}

			return $nodes;
		}

		if (isset($value['@graph']) && is_array($value['@graph'])) {
			$nodes = $this->extractNodes($value['@graph']);

			if (isset($value['@type'])) {
				$parent = $value;
				unset($parent['@graph']);
				$nodes[] = $this->makeNode($parent);
			}

			return $nodes;
		}

		return [$this->makeNode($value)];
	}

	private function makeNode(array $data): Node
	{
		$node = new Node();
		$node->data = $data;
		$node->type = $data['@type'] ?? null;
		$node->id = is_string($data['@id'] ?? null) ? $data['@id'] : null;
		$node->inLanguage = is_string($data['inLanguage'] ?? null) ? $data['inLanguage'] : null;

		$url = $data['url'] ?? null;

		if (is_string($url)) {
			$node->url = $this->config->shouldProcessUrls()
				? Utils::processUrl($url, $this->config)
				: $url;
		}

		return $node;
	}
}
