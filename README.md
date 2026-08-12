<p align="center">
    <img src="https://raw.githubusercontent.com/osmuhin/html-meta/refs/heads/master/docs/logo.svg" alt="HTML meta logo" width="320">
</p>

<p align="center">
    <img src="https://github.com/osmuhin/html-meta/actions/workflows/tests.yml/badge.svg" alt="Tests status">
    <a href="https://packagist.org/packages/osmuhin/html-meta"><img src="https://poser.pugx.org/osmuhin/html-meta/d/total.svg" alt="Total Downloads"></a>
    <img src="https://poser.pugx.org/osmuhin/html-meta/license.svg" alt="License">
</p>

**HTML Meta** is a PHP package for parsing website metadata, such as titles, favicons, OpenGraph tags and others.

---

## Installation

To install the package via Composer, run:

```bash
composer require osmuhin/html-meta
```

> [!NOTE]
> Ensure that the vendor/autoload.php file is required in your code to enable the autoloading mechanism provided by [Composer](https://getcomposer.org/doc/01-basic-usage.md).

## Basic usage

### Parsing Metadata from URL

```php
use Osmuhin\HtmlMeta\Crawler;

$meta = Crawler::init(url: 'https://google.com')->run();

echo $meta->title; // Google
```

### Parsing Metadata from Raw HTML

Instead of URL, you can parse metadata from Raw HTML passing it as a string:

```php
$html = <<<END
<html lang="en">
    <head>
        <title>Google</title>
        <meta charset="UTF-8">
        <link rel="icon" href="/favicon.ico">
    </head>
</html>
END;

$meta = Crawler::init(html: $html, url: 'https://google.com')->run();

$icon = $meta->favicon->icons[0];

echo $icon->url; // https://google.com/favicon.ico
```

> Always pass the `url` parameter when using raw HTML to resolve relative paths correctly.
>
> Relative URL resolution follows RFC 3986: a base without a trailing slash treats the last path segment as a file. Use a trailing slash (e.g. `https://example.com/dir/`) when the base should act as a directory.

### Using a Custom Request Object

Under the hood, the [GuzzleHttp](https://docs.guzzlephp.org/en/stable/) library is used to get html, so you can create your own request object and pass it as a `$request` parameter:

```php
$request = new \GuzzleHttp\Psr7\Request('GET', 'https://google.com');

$meta = Crawler::init(request: $request)->run();
```

> [!WARNING]
> When crawling URLs from untrusted input, be aware of SSRF risks (the crawler follows redirects by default). Prefer injecting a hardened Guzzle client via `setGuzzleClient()` (timeouts, allowlists, disabled private networks) instead of passing raw user URLs to `Crawler::init(url: ...)`.

```php
$client = new \GuzzleHttp\Client([
    'timeout' => 5,
    'allow_redirects' => false,
]);

$meta = Crawler::init(url: 'https://example.com')
    ->setGuzzleClient($client)
    ->run();
```

All properties of the `meta` object are described [**here**](/docs/meta-object-properties.md).

## Configuration
<a name="config"></a>

You can customize the crawler’s behavior using its configuration methods:

```php
$crawler = Crawler::init(url: 'https://google.com');
$crawler->config
    ->dontProcessUrls()
    ->dontUseTypeConversions()
    ->processUrlsWith('https://yandex.ru')
    ->dontUseDefaultDistributorsConfiguration();
```

| Setting | Description |
|---------|-------------|
| ```dontProcessUrls()``` | Disables the conversion of relative URLs to absolute URLs. |
| ```dontUseTypeConversions()``` | Disables automatic type conversions (e.g., string to int): <br><br> ```<meta property="og:image:height" content="630">``` <br> Using type conversions: ```int(630)``` <br> Disabled type conversions: ```string(3) "630"``` <br><br> ```<meta property="og:image:height" content="630.5">``` <br> Using type conversions: `null` <br> Disabled type conversions: ```string(5) "630.5"``` |
| ```processUrlsWith(string $url)``` | Sets a base URL for resolving relative paths (automatically enables URL processing). |
| ```dontUseDefaultDistributorsConfiguration()``` | Disables the default distributor configuration. |

## Core concepts

### The Crawler object

The main interaction happens through the `$crawler` object of type `\Osmuhin\HtmlMeta\Crawler`. <br>

1. Initialization: Configure the crawler before `run()` calling.

2. Execution: After `run()` calling, the crawler performs the following steps:
    * fetches the HTML string from the URL (if raw HTML is not provided). <br>
    The priority of the parameters, if they are more than 1 is following: `string $html` ➡ `\GuzzleHttp\Psr7\Request $request` ➡ `string $url`;

    * parses the HTML using the configured xpath:

        ```php
        $crawler->xpath = '//html|//html/head/link|//html/head/meta|//html/head/title';
        ```

        > You are free to overwrite xpath property;

    * passes the parsed elements to the distributor stack;

    * the found HTML element is passed to the distributor stack <br>
    If the HTML element passed the conditions, then its value is written to [DTO (Data Transfer Object)](https://en.wikipedia.org/wiki/Data_transfer_object) of the type `\Osmuhin\HtmlMeta\Contracts\Dto`;

    * after parsing the HTML string, the root DTO `\Osmuhin\HtmlMeta\Dto\Meta` is formed in output.

### Distributors

A Distributor validates HTML elements and distributes their data into DTOs.

Distributor must implement the interface `\Osmuhin\HtmlMeta\Contracts\Distributor` and has 2 main methods:

```php
public function canHandle(): bool
{

}

public function handle(): void
{

}
```

```canHandle()``` - Checks whether the distributor can handle the current element.
If returns true, then all sub-distributors are polled, and then the handle method is called.

```handle()``` - Distributes the HTML element data by DTOs according to its own rules.

You can view the structure of the simplest [TitleDistributor](/src/Distributors/TitleDistributor.php) distributor:

```php
class TitleDistributor extends \Osmuhin\HtmlMeta\Distributors\AbstractDistributor
{
    public function canHandle(): bool
    {
        return $this->el->name === 'title';
    }

    public function handle(): void
    {
        $this->meta->title = $this->el->innerText;
    }
}
```

You are free to replace some kind distributor of your own, example:

```php
use Osmuhin\HtmlMeta\Distributors\TitleDistributor;

class MyCustomTitleDistributor extends TitleDistributor
{
    public function handle(): void
    {
        $this->meta->title = 'Prefix for title ' . $this->el->innerText;
    }
}
```

replace original `TitleDistributor` in initial configuration:
```php
$crawler = Crawler::init(url: 'https://google.com');
$crawler->distributor->setSubDistributor(
    MyCustomTitleDistributor::class,
    TitleDistributor::class
);

$meta = $crawler->run();
$meta->title === 'Prefix for title Google';
```

... or even overwrite the distributors tree completely:

```php
$crawler = Crawler::init(url: 'https://google.com');
$crawler->xpath = '//html/head/title';
$crawler->config->dontUseDefaultDistributorsConfiguration();

$crawler->distributor->useSubDistributors(
    MyCustomTitleDistributor::init($crawler->context)
);

$meta = $crawler->run();
```

<details>
<summary>Default distributors configuration</summary>

```php
$crawler->distributor->useSubDistributors(
    \Osmuhin\HtmlMeta\Distributors\HtmlDistributor::init($crawler->context),
    \Osmuhin\HtmlMeta\Distributors\TitleDistributor::init($crawler->context),
    \Osmuhin\HtmlMeta\Distributors\MetaDistributor::init($crawler->context)->useSubDistributors(
        \Osmuhin\HtmlMeta\Distributors\HttpEquivDistributor::init($crawler->context),
        \Osmuhin\HtmlMeta\Distributors\TwitterDistributor::init($crawler->context),
        \Osmuhin\HtmlMeta\Distributors\OpenGraphDistributor::init($crawler->context)
    ),
    \Osmuhin\HtmlMeta\Distributors\LinkDistributor::init($crawler->context)->useSubDistributors(
        \Osmuhin\HtmlMeta\Distributors\LinkRelDistributor::init($crawler->context)->useSubDistributors(
            \Osmuhin\HtmlMeta\Distributors\FaviconDistributor::init($crawler->context)
        )
    )
);
```
</details>

## Contributing

Thank you for considering contributing to this package! Please refer to the [Contributing Guidelines](CONTRIBUTING.md) for more details.

You can contact me or just come say hi in Telegram: [@wischerdson](https://t.me/wischerdson)

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
