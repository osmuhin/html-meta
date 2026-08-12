<?php

namespace Osmuhin\HtmlMeta\Dto;

use Osmuhin\HtmlMeta\Contracts\Dto;

/**
 * The http-equiv attribute in HTML is used inside the <meta> tag to transmit meta information
 * that affects the behavior of the browser or web server. It "emulates" HTTP headers transmitted
 * by the server.
 */
class HttpEquiv implements Dto
{
	/**
	 * Specifies the content type and page encoding.
	 * Example: <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"\>
	 */
	public ?string $contentType = null;

	/**
	 * Specifies the compatibility mode for Internet Explorer.
	 * Example: <meta http-equiv="X-UA-Compatible" content="IE=edge"\>.
	 */
	public ?string $xUaCompatible = null;

	/**
	 * Manages page caching.
	 * Example: <meta http-equiv="Cache-Control" content="no-cache"\>.
	 */
	public ?string $cacheControl = null;

	/**
	 * Specifies the language of the page.
	 * Example: <meta http-equiv="Content-Language" content="en"\>.
	 */
	public ?string $contentLanguage = null;

	/**
	 * An analogue of Cache-Control, but less reliable. Specifies that the page should not be cached.
	 * Example: <meta http-equiv="pragma" content="no-cache"\>.
	 */
	public ?string $pragma = null;

	/**
	 * Sets the expiration date and time of the page in the cache.
	 * Example: <meta http-equiv="expires" content="Tue, January 01, 2025, 12:00:00 GMT"\>.
	 */
	public ?string $expires = null;

	/**
	 * Reloads the page after a specified interval or redirects.
	 * Example: <meta http-equiv="refresh" content="5; url=https://example.com"\>.
	 */
	public ?string $refresh = null;

	/**
	 * Manages the security policy, for example, the loading of scripts and styles.
	 * Example: <meta http-equiv="Content-Security-Policy" content="default-src 'self';"\>.
	 */
	public ?string $contentSecurityPolicy = null;

	/**
	 * Manages the preloading of DNS queries.
	 * Example: <meta http-equiv="x-dns-prefetch-control" content="on"\>.
	 */
	public ?string $xDnsPrefetchControl = null;

	/**
	 * Specifies the domains that can access the resource (CORS).
	 * Example: <meta http-equiv="Access-Control-Allow-Origin" content="*"\>.
	 */
	public ?string $accessControlAllowOrigin = null;

	/**
	 * All <meta http-equiv> elements that are non-standard.
	 * [http-equiv => content]
	 */
	public array $other = [];

	public function toArray(): array
	{
		return [
			'contentType' => $this->contentType,
			'xUaCompatible' => $this->xUaCompatible,
			'cacheControl' => $this->cacheControl,
			'contentLanguage' => $this->contentLanguage,
			'pragma' => $this->pragma,
			'expires' => $this->expires,
			'refresh' => $this->refresh,
			'contentSecurityPolicy' => $this->contentSecurityPolicy,
			'xDnsPrefetchControl' => $this->xDnsPrefetchControl,
			'accessControlAllowOrigin' => $this->accessControlAllowOrigin,
			'other' => $this->other,
		];
	}
}
