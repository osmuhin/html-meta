<?php

namespace Osmuhin\HtmlMeta\DataMappers;

/**
 * Maps http-equiv meta tags onto the HttpEquiv DTO.
 *
 * @codeCoverageIgnore
 */
class HttpEquivDataMapper extends AbstractDataMapper
{
	protected function getMap(): array
	{
		return [
			'content-type' => 'contentType',
			'x-ua-compatible' => 'xUaCompatible',
			'cache-control' => 'cacheControl',
			'content-language' => 'contentLanguage',
			'pragma' => 'pragma',
			'expires' => 'expires',
			'refresh' => 'refresh',
			'content-security-policy' => 'contentSecurityPolicy',
			'x-dns-prefetch-control' => 'xDnsPrefetchControl',
			'access-control-allow-origin' => 'accessControlAllowOrigin'
		];
	}

	public function assign(string $key, string $content)
	{
		return $this->assignAccordingToTheMap(
			$this->getMap(),
			$this->meta->httpEquiv,
			$key,
			$content
		);
	}
}
