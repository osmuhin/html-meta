<?php

namespace Osmuhin\HtmlMeta\DataMappers;

/**
 * Maps named `<meta name="...">` attributes onto Meta DTO fields.
 *
 * @codeCoverageIgnore
 */
class MetaDataMapper extends AbstractDataMapper
{
	protected function getMap(): array
	{
		return [
			'apple-itunes-app' => 'appleItunesApp',
			'apple-mobile-web-app-capable' => 'appleMobileWebAppCapable',
			'apple-mobile-web-app-status-bar-style' => 'appleMobileWebAppStatusBarStyle',
			'application-name' => 'applicationName',
			'author' => 'author',
			'color-scheme' => 'colorScheme',
			'copyright' => 'copyright',
			'description' => 'description',
			'format-detection' => 'formatDetection',
			'generator' => 'generator',
			'keywords' => 'keywords',
			'referrer' => 'referrer',
			'robots' => 'robots',
			'viewport' => 'viewport'
		];
	}

	public function assign(string $key, string $content): bool
	{
		return $this->assignAccordingToTheMap(
			$this->getMap(),
			$this->meta,
			$key,
			$content
		);
	}
}
