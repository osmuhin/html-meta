<?php

namespace Osmuhin\HtmlMeta\Dto;

use Osmuhin\HtmlMeta\Contracts\Dto;

class Meta implements Dto
{
	/**
	 * A charset declaration, giving the character encoding in which the document is encoded.
	 * Example: <meta charset="utf-8"\>
	 */
	public ?string $charset = null;

	/**
	 * Specifies one or more color schemes with which the document is compatible.
	 * Example: <meta name="color-scheme" content="light dark"\>
	 */
	public ?string $colorScheme = null;

	/**
	 * The name of the application running in the web page.
	 * Example: <meta name="application-name" content="Amazon"\>
	 */
	public ?string $applicationName = null;

	/**
	 * Specifies the ownership of the copyright.
	 * Example: <meta name="copyright" content="Apple Inc."\>
	 */
	public ?string $copyright = null;

	/**
	 * A short and accurate summary of the content of the page.
	 * <meta name="description" content="Some description"\>
	 */
	public ?string $description = null;

	/**
	 * The name of the document's author.
	 * <meta name="author" content="Osmuhin Daniil"\>
	 */
	public ?string $author = null;

	/**
	 * The identifier of the software that generated the page.
	 * <meta name="generator" content="WordPress.com"\>
	 */
	public ?string $generator = null;

	/**
	 * The language of the document content.
	 * Example: <html lang="en_US"\></html\>
	 */
	public ?string $lang = null;

	/**
	 * Contains the absolute or partial address from which a resource has been requested.
	 * Example: <meta name="referrer" content="origin"\>
	 */
	public ?string $referrer = null;

	/**
	 * Indicates a suggested color that user agents should use to customize the display of the
	 * page or of the surrounding user interface.
	 * The content attribute contains a valid CSS color.
	 * Examples:
	 *     <meta name="theme-color" content="#4285f4"\>
	 *     <meta name="theme-color" media="(prefers-color-scheme: light)" content="cyan"\>
	 */
	public array $themeColor = [];

	/**
	 * Document's title that is shown in a browser's title bar or a page's tab.
	 * Example: <title\>Some title</title\>
	 */
	public ?string $title = null;

	/**
	 * Viewport gives a browser instructions on how to control the page's dimensions and scaling.
	 * Example: <meta name="viewport" content="width=device-width, initial-scale=1"\>
	 */
	public ?string $viewport = null;

	/**
	 * Words relevant to the page's content separated by commas.
	 * Example: <meta name="keywords" content="money, exchange"\>
	 */
	public ?string $keywords = null;

	/**
	 * The behavior that cooperative crawlers, or "robots", should use with the page.
	 * Example: <meta name="robots" content="noindex"\>
	 */
	public ?string $robots = null;

	/**
	 * Sets whether a web application runs in full-screen mode.
	 * Example: <meta name="apple-mobile-web-app-capable" content="yes"\>
	 */
	public ?string $appleMobileWebAppCapable = null;

	/**
	 * Sets the style of the status bar for a web application.
	 * Example: <meta name="apple-mobile-web-app-status-bar-style" content="black"\>
	 */
	public ?string $appleMobileWebAppStatusBarStyle = null;

	/**
	 * Enables or disables automatic detection of possible phone numbers in a webpage.
	 * Example: <meta name="format-detection" content="telephone=no"\>
	 */
	public ?string $formatDetection = null;

	/**
	 * Sets the direction of text output inside the element.
	 * Example: <html dir="ltr"\></html\>
	 */
	public ?string $dir = null;

	/**
	 * Implements a smart app banner on the website.
	 * Example: <meta name="apple-itunes-app" content="app-id=myAppStoreID, app-argument=myURL"\>
	 */
	public ?string $appleItunesApp = null;

	/**
	 * The rel="canonical" attribute in the <link> tag indicates to the search robot that a
	 * particular page is canonical, that is, the main page.
	 * Example: <link rel="canonical" href="/page/" \>
	 */
	public ?string $canonical = null;

	public array $htmlAttributes = [];

	public Twitter $twitter;

	public Favicon $favicon;

	public OpenGraph $openGraph;

	public HttpEquiv $httpEquiv;

	public array $unrecognizedMeta = [];

	public function __construct()
	{
		$this->openGraph = new OpenGraph();
		$this->twitter = new Twitter();
		$this->favicon = new Favicon();
		$this->httpEquiv = new HttpEquiv();
	}

	public function toArray(): array
	{
		return [
			'appleItunesApp' => $this->appleItunesApp,
			'appleMobileWebAppCapable' => $this->appleMobileWebAppCapable,
			'appleMobileWebAppStatusBarStyle' => $this->appleMobileWebAppStatusBarStyle,
			'applicationName' => $this->applicationName,
			'author' => $this->author,
			'charset' => $this->charset,
			'colorScheme' => $this->colorScheme,
			'copyright' => $this->copyright,
			'description' => $this->description,
			'dir' => $this->dir,
			'favicon' => $this->favicon->toArray(),
			'formatDetection' => $this->formatDetection,
			'generator' => $this->generator,
			'htmlAttributes' => $this->htmlAttributes,
			'httpEquiv' => $this->httpEquiv->toArray(),
			'keywords' => $this->keywords,
			'lang' => $this->lang,
			'openGraph' => $this->openGraph->toArray(),
			'referrer' => $this->referrer,
			'robots' => $this->robots,
			'themeColor' => $this->themeColor,
			'title' => $this->title,
			'twitter' => $this->twitter->toArray(),
			'viewport' => $this->viewport,
			'canonical' => $this->canonical,
			'unrecognizedMeta' => $this->unrecognizedMeta,
		];
	}
}
