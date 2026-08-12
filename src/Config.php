<?php

namespace Osmuhin\HtmlMeta;

/**
 * Runtime configuration for URL processing, type conversion, and distributor setup.
 */
class Config
{
	private string $baseUrl = '';

	private bool $useDefaultDistributorsConfigurationFlag = true;

	private bool $shouldProcessUrlsFlag = true;

	private bool $useTypeConversionFlag = true;

	/** Disable converting relative URLs to absolute ones. */
	public function dontProcessUrls($doNot = true): self
	{
		$this->shouldProcessUrlsFlag = !$doNot;

		return $this;
	}

	/** Disable automatic type conversions (e.g. digit strings to int). */
	public function dontUseTypeConversions($doNot = true): self
	{
		$this->useTypeConversionFlag = !$doNot;

		return $this;
	}

	/**
	 * Set the base URL used when resolving relative paths and enable URL processing.
	 *
	 * RFC 3986: without a trailing slash, the last path segment is treated as a file.
	 */
	public function processUrlsWith(string $baseUrl): self
	{
		$this->shouldProcessUrlsFlag = true;
		$this->setBaseUrl($baseUrl);

		return $this;
	}

	/** Skip installing the default distributor tree so you can configure your own. */
	public function dontUseDefaultDistributorsConfiguration($doNot = true): self
	{
		$this->useDefaultDistributorsConfigurationFlag = !$doNot;

		return $this;
	}

	public function shouldUseTypeConversion(): bool
	{
		return $this->useTypeConversionFlag;
	}

	public function shouldUseDefaultDistributorsConfiguration(): bool
	{
		return $this->useDefaultDistributorsConfigurationFlag;
	}

	public function shouldProcessUrls(): bool
	{
		return $this->shouldProcessUrlsFlag;
	}

	public function setBaseUrl(string $baseUrl): self
	{
		$this->baseUrl = $baseUrl;

		return $this;
	}

	/**
	 * @return array{0: string, 1: string} Origin (scheme + authority) and path without leading slash
	 */
	public function getBaseUrl(): array
	{
		return Utils::splitUrl($this->baseUrl);
	}

	/** Full base URI string used by {@see Utils::processUrl()}. */
	public function getBaseUrlString(): string
	{
		return $this->baseUrl;
	}
}
