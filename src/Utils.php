<?php

namespace Osmuhin\HtmlMeta;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Symfony\Component\Mime\MimeTypes;

/**
 * Shared helpers for MIME guessing and URL resolution.
 */
class Utils
{
	/** Guess a MIME type from a file extension using Symfony MimeTypes. */
	public static function guessMimeType(string $extension): ?string
	{
		$types = MimeTypes::getDefault()->getMimeTypes($extension);

		return $types ? $types[0] : null;
	}

	/** Return the file extension from a path, or null if none. */
	public static function guessExtension(string $path): ?string
	{
		$explodedPath = explode('/', $path);
		$file = array_pop($explodedPath);

		$explodedName = explode('.', $file);

		return \count($explodedName) > 1 ? array_pop($explodedName) : null;
	}

	/**
	 * Resolve `$url` against the configured base URL (RFC 3986 via PSR-7 UriResolver).
	 *
	 * Returns `$url` unchanged when the base URL is empty.
	 */
	public static function processUrl(string $url): string
	{
		/** @var \Osmuhin\HtmlMeta\Config $config */
		$config = ServiceLocator::container()->get(Config::class);

		$base = $config->getBaseUrlString();

		if ($base === '') {
			return $url;
		}

		return (string) UriResolver::resolve(new Uri($base), new Uri($url));
	}

	/**
	 * Split a URL into origin and path for {@see Config::getBaseUrl()}.
	 *
	 * @return array{0: string, 1: string} Origin (scheme + authority) and path without leading slash
	 */
	public static function splitUrl(string $url): array
	{
		$uri = new Uri($url);
		$scheme = $uri->getScheme();
		$authority = $uri->getAuthority();

		if ($scheme !== '' && $authority !== '') {
			$domain = $scheme . '://' . $authority;
		} elseif ($authority !== '') {
			$domain = '//' . $authority;
		} else {
			$domain = '';
		}

		$path = trim($uri->getPath(), '/');

		return [$domain, $path];
	}
}
