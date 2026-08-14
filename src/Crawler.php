<?php

namespace Osmuhin\HtmlMeta;

use Composer\InstalledVersions as ComposerInstalledVersions;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Osmuhin\HtmlMeta\Contracts\Distributor;
use Osmuhin\HtmlMeta\Distributors\AbstractDistributor;
use Osmuhin\HtmlMeta\Dto\Meta;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Entry point for parsing HTML metadata from a URL, raw HTML, or a Guzzle request.
 */
class Crawler
{
	public readonly Distributor $distributor;

	public readonly Config $config;

	public readonly Context $context;

	/** XPath used to select HTML elements that distributors may handle. */
	public string $xpath = '//html|//html/head/link|//html/head/meta|//html/head/title|//script';

	private string $html;

	private string $url;

	private Meta $meta;

	private GuzzleClient $guzzleClient;

	private GuzzleRequest $guzzleRequest;

	public function __construct()
	{
		$this->meta = new Meta();
		$this->config = new Config();
		$this->context = new Context($this->meta, $this->config);
		$this->distributor = $this->makeAnonymousDistributor();
	}

	/**
	 * Create a crawler configured with optional HTML, URL, and/or Guzzle request.
	 *
	 * Parameter priority when fetching HTML: `$html` → `$request` → `$url`.
	 */
	public static function init(?string $html = null, ?string $url = null, ?GuzzleRequest $request = null): self
	{
		$crawler = new self();

		$html && $crawler->setHtml($html);
		$url && $crawler->setUrl($url);
		$request && $crawler->setRequest($request);

		return $crawler;
	}

	/** Provide raw HTML to parse (skips HTTP fetch). */
	public function setHtml(string $html): self
	{
		$this->html = $html;

		return $this;
	}

	/** Set the page URL and use it as the base for relative URL resolution. */
	public function setUrl(string $url): self
	{
		$this->config->processUrlsWith($this->url = $url);

		return $this;
	}

	/** Use a custom Guzzle request; its URI becomes the base URL. */
	public function setRequest(GuzzleRequest $request): self
	{
		$this->guzzleRequest = $request;

		$this->setUrl((string) $request->getUri());

		return $this;
	}

	/** Inject a custom Guzzle client (timeouts, redirect policy, SSRF hardening, etc.). */
	public function setGuzzleClient(GuzzleClient $guzzleClient): self
	{
		$this->guzzleClient = $guzzleClient;

		return $this;
	}

	/**
	 * Fetch (if needed), parse HTML, and return the populated Meta DTO.
	 *
	 * @throws RuntimeException When neither HTML, URL, nor request was provided
	 */
	public function run(): Meta
	{
		$html = $this->resolveHtmlString();

		$this->config->shouldUseDefaultDistributorsConfiguration() &&
		$this->useDefaultDistributorsConfiguration();

		$crawler = new DomCrawler($html);

		foreach ($crawler->filterXPath($this->xpath) as $node) {
			$this->distributor->el = new Element($node);
			$this->distributor->handle();
		}

		return $this->meta;
	}

	private function makeAnonymousDistributor(): Distributor
	{
		$context = $this->context;

		return new class($context) extends AbstractDistributor {
			public function canHandle(): bool
			{
				return true;
			}

			public function handle(): void
			{
				$this->pollSubDistributors();
			}
		};
	}

	private function useDefaultDistributorsConfiguration()
	{
		$context = $this->context;

		$this->distributor->useSubDistributors(
			\Osmuhin\HtmlMeta\Distributors\HtmlDistributor::init($context),
			\Osmuhin\HtmlMeta\Distributors\TitleDistributor::init($context),
			\Osmuhin\HtmlMeta\Distributors\MetaDistributor::init($context)->useSubDistributors(
				\Osmuhin\HtmlMeta\Distributors\HttpEquivDistributor::init($context),
				\Osmuhin\HtmlMeta\Distributors\TwitterDistributor::init($context),
				\Osmuhin\HtmlMeta\Distributors\OpenGraphDistributor::init($context)
			),
			\Osmuhin\HtmlMeta\Distributors\LinkDistributor::init($context)->useSubDistributors(
				\Osmuhin\HtmlMeta\Distributors\LinkRelDistributor::init($context)->useSubDistributors(
					\Osmuhin\HtmlMeta\Distributors\FaviconDistributor::init($context)
				)
			),
			\Osmuhin\HtmlMeta\Distributors\JsonLdDistributor::init($context)
		);
	}

	private function makeGuzzleClient(): GuzzleClient
	{
		if (isset($this->guzzleClient)) {
			return $this->guzzleClient;
		}

		$version = ComposerInstalledVersions::getPrettyVersion('osmuhin/html-meta');

		return new GuzzleClient([
			'headers' => [
				'User-Agent' => "OsmuhinHtmlMetaCrawler/{$version}",
				'Accept' => 'text/html,application/xhtml+xml,application/xml'
			]
		]);
	}

	private function makeGuzzleRequest(): GuzzleRequest
	{
		if (isset($this->guzzleRequest)) {
			return $this->guzzleRequest;
		}

		return new GuzzleRequest('GET', $this->url);
	}

	/**
	 * @throws \RuntimeException
	 */
	private function resolveHtmlString(): string
	{
		if (!isset($this->html) && !isset($this->url) && !isset($this->guzzleRequest)) {
			throw new RuntimeException('An HTML string or a url, or a guzzle request object must be provided for parsing.');
		}

		if (isset($this->html)) {
			return $this->html;
		}

		$response = $this->makeGuzzleClient()->send(
			$this->makeGuzzleRequest()
		);

		return $response->getBody()->getContents();
	}
}
