# Properties of output meta object

## Root object

### appleItunesApp
`string` `null`

<!-- ### appleItunesApp `string` `null` -->

<details>
<summary>Example</summary>

```html
<meta name="apple-itunes-app" content="app-id=myAppStoreID, app-argument=myURL">
```

```php
$meta->appleItunesApp === 'app-id=myAppStoreID, app-argument=myURL';
```
</details>

### appleMobileWebAppCapable
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="apple-mobile-web-app-capable" content="yes">
```

```php
$meta->appleMobileWebAppCapable === 'yes';
```
</details>

### appleMobileWebAppStatusBarStyle
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="apple-mobile-web-app-status-bar-style" content="black">
```

```php
$meta->appleMobileWebAppStatusBarStyle === 'black';
```
</details>

### applicationName
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="application-name" content="Amazon">
```

```php
$meta->applicationName === 'Amazon';
```
</details>

### author
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="author" content="Osmuhin Daniil">
```

```php
$meta->author === 'Osmuhin Daniil';
```
</details>

### canonical
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<link rel="canonical" href="/catalog/123123">
```

```php
$meta->canonical === 'https://example.com/catalog/123123';
```
</details>

### charset
`string` `null`

<details>
<summary>Example</summary>

```html
<meta charset="utf-8">
```

```php
$meta->charset === 'utf-8';
```
</details>

### colorScheme
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="color-scheme" content="light dark">
```

```php
$meta->colorScheme === 'light dark';
```
</details>

### copyright
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="copyright" content="Apple Inc.">
```

```php
$meta->copyright === 'Apple Inc.';
```
</details>

### description
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="description" content="Some description">
```

```php
$meta->description === 'Some description';
```
</details>

### dir
`string` `null`

<details>
<summary>Example</summary>

```html
<html dir="ltr"></html>
```

```php
$meta->dir === 'ltr';
```
</details>

### favicon
[`Favicon`](#favicon-object)

<details>
<summary>Example</summary>

```html
<link rel="shortcut icon" href="favicon-small.ico" sizes="16X14">
<link rel="apple-touch-icon" href="apple.com/apple-touch-icon.png" sizes="any">
<link rel="manifest" href="/manifest.webmanifest">
```

```php
$meta->favicon->toArray() === [
    'manifest' => 'http://example.com/manifest.webmanifest',
    'icons' => [
        [
            'url' => 'http://example.com/path/favicon-small.ico',
            'mime' => 'application/ico',
            'extension' => 'ico',
            'width' => 16,
            'height' => 14,
            'sizes' => '16x14'
        ]
    ],
    'appleTouchIcons' => [
        [
            'url' => 'http://example.com/path/apple.com/apple-touch-icon.png',
            'mime' => 'image/png',
            'extension' => 'png',
            'width' => null,
            'height' => null,
            'sizes' => 'any'
        ]
    ],
];
```
</details>

### formatDetection
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="format-detection" content="telephone=no">
```

```php
$meta->formatDetection === 'telephone=no';
```
</details>

### generator
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="generator" content="WordPress.com">
```

```php
$meta->generator === 'WordPress.com';
```
</details>

### htmlAttributes
`array<string>`

<details>
<summary>Example</summary>

```html
<html lang="en" data-theme="dark" dir="rtl"></html>
```

```php
$meta->htmlAttributes === [
    'lang' => 'en',
    'data-theme' => 'dark',
    'dir' => 'rtl'
];
```
</details>

### httpEquiv
[`HttpEquiv`](#httpequiv-object)

<details>
<summary>Example</summary>

```html
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Language" content="en">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="Tue, January 01, 2025, 12:00:00 GMT">
<meta http-equiv="refresh" content="5; url=https://example.com">
<meta http-equiv="Content-Security-Policy" content="default-src 'self';">
<meta http-equiv="x-dns-prefetch-control" content="on">
<meta http-equiv="Access-Control-Allow-Origin" content="*">
<meta http-equiv="non-standart-meta" content="some-value">
```

```php
$meta->httpEquiv->toArray() === [
    'contentType' => 'text/html; charset=UTF-8',
    'xUaCompatible' => 'IE=edge',
    'cacheControl' => 'no-cache',
    'contentLanguage' => 'en',
    'pragma' => 'no-cache',
    'expires' => 'Tue, January 01, 2025, 12:00:00 GMT',
    'refresh' => '5; url=https://example.com',
    'contentSecurityPolicy' => "default-src 'self';",
    'xDnsPrefetchControl' => 'on',
    'accessControlAllowOrigin' => '*',
    'other' => ['non-standart-meta' => 'some-value']
];
```
</details>

### keywords
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="keywords" content="money, exchange">
```

```php
$meta->keywords === 'money, exchange';
```
</details>

### lang
`string` `null`

<details>
<summary>Example</summary>

```html
<html lang="en_US"></html>
```

```php
$meta->lang === 'en_US';
```
</details>

### openGraph
[`OpenGraph`](#opengraph-object)

<details>
<summary>Example</summary>

```html
<meta property="og:type" content="website">
<meta property="og:title" content="Page title">
<meta property="og:description" content="Description of the page that will be displayed in the posts.">
<meta property="og:url" content="products">
<meta property="og:image" content="/image.jpg">
<meta property="og:image:alt" content="Image description">

<meta property="og:site_name" content="Site name">
<meta property="og:locale" content="ru_RU">
<meta property="og:locale:alternate" content="en_US">

<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<meta property="og:audio" content="https://example.com/audio.mp3">
<meta property="og:audio:secure_url" content="https://example.com/audio.mp3">
<meta property="og:audio:type" content="audio/mpeg">

<meta property="og:video" content="https://example.com/video.mp4">
<meta property="og:video:secure_url" content="https://example.com/video.mp4">
<meta property="og:video:type" content="video/mp4">
<meta property="og:video:width" content="1280">
<meta property="og:video:height" content="720">

<meta property="og:determiner" content="a">
```

```php
$meta->openGraph->toArray() === [
    'title' => 'Page title',
    'type' => 'website',
    'url' => 'https://example.com/path/products',
    'description' => 'Description of the page that will be displayed in the posts.',
    'determiner' => 'a',
    'siteName' => 'Site name',
    'locale' => 'ru_RU',
    'alternateLocales' => ['en_US'],
    'images' => [
        [
            'url' => 'https://example.com/image.jpg',
            'secureUrl' => null,
            'type' => 'image/jpeg',
            'width' => 1200,
            'height' => 630,
            'alt' => 'Image description',
        ]
    ],
    'videos' => [
        [
            'url' => 'https://example.com/video.mp4',
            'secureUrl' => 'https://example.com/video.mp4',
            'type' => 'video/mp4',
            'width' => 1280,
            'height' => 720
        ]
    ],
    'audio' => [
        [
            'url' => 'https://example.com/audio.mp3',
            'secureUrl' => 'https://example.com/audio.mp3',
            'type' => 'audio/mpeg'
        ]
    ]
];
```
</details>

### referrer
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="referrer" content="origin">
```

```php
$meta->referrer === 'origin';
```
</details>

### robots
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="robots" content="noindex">
```

```php
$meta->robots === 'noindex';
```
</details>

### themeColor
`array<string>`

<details>
<summary>Example</summary>

```html
<meta name="theme-color" content="#eb0c0c">
<meta name="theme-color" media="(prefers-color-scheme: light)" content="#fff">
```

```php
$meta->themeColor === [
    0 => '#eb0c0c',
    '(prefers-color-scheme: light)' => '#fff'
];
```
</details>

### title
`string` `null`

> [!NOTE]
> The title can be set in two ways: via the `title` tag or via the `meta` tag. Priority is given to the `title` tag if both variants are presented in the html document.

<details>
<summary>Example</summary>

```html
<title>Google</title>
<meta name="title" content="DuckDuckGo">
```

```php
$meta->title === 'Google';
```
</details>

### twitter
[`Twitter`](#twitter-object)

<details>
<summary>Example</summary>

```html
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@username">
<meta name="twitter:title" content="Laravel - The PHP Framework For Web Artisans">
<meta name="twitter:description" content="Some description for twitter card.">
<meta name="twitter:image" content="/image.jpg">
<meta name="twitter:image:alt" content="A description of the image">
<meta name="twitter:creator" content="@author">
<meta name="twitter:app:id:iphone" content="123456789">
```

```php
$meta->twitter->toArray() === [
    'card' => 'summary',
    'site' => '@username',
    'title' => 'Laravel - The PHP Framework For Web Artisans',
    'description' => 'Some description for twitter card.',
    'image' => 'https://example.com/image.jpg',
    'imageAlt' => 'A description of the image',
    'creator' => '@author',
    'other' => ['twitter:app:id:iphone' => '123456789'],
];
```
</details>

### viewport
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="viewport" content="width=device-width, initial-scale=1">
```

```php
$meta->viewport === 'width=device-width, initial-scale=1';
```
</details>

### unrecognizedMeta
`array<string, string>`

<details>
<summary>Example</summary>

```html
<meta property="fb:app_id" content="123456789012345">
```

```php
$meta->unrecognizedMeta === ['fb:app_id' => '123456789012345'];
```
</details>

<a name="favicon-object"><h2>Favicon object</h2></a>

### manifest
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<link rel="manifest" href="/manifest.webmanifest">
```

```php
$meta->favicon->manifest === 'https://example.com/manifest.webmanifest';
```
</details>

### icons
<code>array<<a href="#icon-object">Icon</a>></code>

<details>
<summary>Example</summary>

```html
<link rel="shortcut icon" href="favicon.ico" sizes="16X14">
```

```php
$meta->favicon->icons[0]->toArray() === [
    'url' => 'http://example.com/path/favicon.ico',
    'mime' => 'application/ico',
    'extension' => 'ico',
    'width' => 16,
    'height' => 14,
    'sizes' => '16x14'
];
```
</details>

### appleTouchIcons
<code>array<<a href="#icon-object">Icon</a>></code>

<details>
<summary>Example</summary>

```html
<link rel="apple-touch-icon" href="apple.com/apple-touch-icon.png">
```

```php
$meta->favicon->appleTouchIcons[0]->toArray() === [
    'url' => 'https://example.com/path/apple.com/apple-touch-icon.png',
    'mime' => 'image/png',
    'extension' => 'png',
    'width' => null,
    'height' => null,
    'sizes' => null
];
```
</details>

<a name="httpequiv-object"><h2>HttpEquiv object</h2></a>

### contentType
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
```

```php
$meta->httpEquiv->contentType === 'text/html; charset=UTF-8';
```
</details>

### xUaCompatible
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="X-UA-Compatible" content="IE=edge">
```

```php
$meta->httpEquiv->xUaCompatible === 'IE=edge';
```
</details>

### cacheControl
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="Cache-Control" content="no-cache">
```

```php
$meta->httpEquiv->cacheControl === 'no-cache';
```
</details>

### contentLanguage
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="Content-Language" content="en">
```

```php
$meta->httpEquiv->contentLanguage === 'en';
```
</details>

### pragma
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="pragma" content="no-cache">
```

```php
$meta->httpEquiv->pragma === 'no-cache';
```
</details>

### expires
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="expires" content="Tue, January 01, 2025, 12:00:00 GMT">
```

```php
$meta->httpEquiv->expires === 'Tue, January 01, 2025, 12:00:00 GMT';
```
</details>

### refresh
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="refresh" content="5; url=https://example.com">
```

```php
$meta->httpEquiv->refresh === '5; url=https://example.com';
```
</details>

### contentSecurityPolicy
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="Content-Security-Policy" content="default-src 'self';">
```

```php
$meta->httpEquiv->contentSecurityPolicy === "default-src 'self';";
```
</details>

### xDnsPrefetchControl
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="x-dns-prefetch-control" content="on">
```

```php
$meta->httpEquiv->xDnsPrefetchControl === 'on';
```
</details>

### accessControlAllowOrigin
`string` `null`

<details>
<summary>Example</summary>

```html
<meta http-equiv="Access-Control-Allow-Origin" content="*">
```

```php
$meta->httpEquiv->accessControlAllowOrigin === '*';
```
</details>

### other
`array<string>`

<details>
<summary>Example</summary>

```html
<meta http-equiv="non-standart-meta" content="some-value">
```

```php
$meta->httpEquiv->other === ['non-standart-meta' => 'some-value'];
```
</details>

<a name="opengraph-object"><h2>OpenGraph object</h2></a>

### title
`string` `null`

<details>
<summary>Example</summary>

```html
<meta property="og:title" content="Page title">
```

```php
$meta->openGraph->title === 'Page title';
```
</details>

### type
`string` `null`

<details>
<summary>Example</summary>

```html
<meta property="og:type" content="website">
```

```php
$meta->openGraph->type === 'website';
```
</details>

### url
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<meta property="og:url" content="products">
```

```php
$meta->openGraph->url === 'https://example.com/products';
```
</details>

### description
`string` `null`

<details>
<summary>Example</summary>

```html
<meta property="og:description" content="Description of the page.">
```

```php
$meta->openGraph->description === 'Description of the page.';
```
</details>

### determiner
`string` `null`

<details>
<summary>Example</summary>

```html
<meta property="og:determiner" content="a">
```

```php
$meta->openGraph->determiner === 'a';
```
</details>

### siteName
`string` `null`

<details>
<summary>Example</summary>

```html
<meta property="og:site_name" content="Site name">
```

```php
$meta->openGraph->siteName === 'Site name';
```
</details>

### locale
`string` `null`

<details>
<summary>Example</summary>

```html
<meta property="og:locale" content="ru_RU">
```

```php
$meta->openGraph->locale === 'ru_RU';
```
</details>

### alternateLocales
`array<string>`

<details>
<summary>Example</summary>

```html
<meta property="og:locale:alternate" content="en_US">
```

```php
$meta->openGraph->alternateLocales === ['en_US'];
```
</details>

### images
<code>array<<a href="#og-image-object">OpenGraph\Image</a>></code>

<details>
<summary>Example</summary>

```html
<meta property="og:image" content="/image.jpg">
<meta property="og:image:alt" content="Image description">
```

```php
$meta->openGraph->images[0]->toArray() === [
    'url' => 'https://example.com/image.jpg',
    'secureUrl' => null,
    'type' => 'image/jpeg',
    'width' => null,
    'height' => null,
    'alt' => 'Image description',
];
```
</details>

### videos
<code>array<<a href="#og-video-object">OpenGraph\Video</a>></code>

<details>
<summary>Example</summary>

```html
<meta property="og:video" content="/video.mp4">
```

```php
$meta->openGraph->videos[0]->toArray() === [
    'url' => 'https://example.com/video.mp4',
    'secureUrl' => null,
    'type' => 'video/mp4',
    'width' => null,
    'height' => null
];
```
</details>

### audio
<code>array<<a href="#og-audio-object">OpenGraph\Audio</a>></code>

<details>
<summary>Example</summary>

```html
<meta property="og:audio" content="/audio.mp3">
```

```php
$meta->openGraph->audio[0]->toArray() === [
    'url' => 'https://example.com/audio.mp3',
    'secureUrl' => null,
    'type' => 'audio/mpeg'
];
```
</details>

<a name="twitter-object"><h2>Twitter object</h2></a>

### card
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="twitter:card" content="summary">
```

```php
$meta->twitter->card === 'summary';
```
</details>

### site
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="twitter:site" content="@username">
```

```php
$meta->twitter->site === '@username';
```
</details>

### title
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="twitter:title" content="Laravel - The PHP Framework For Web Artisans">
```

```php
$meta->twitter->title === 'Laravel - The PHP Framework For Web Artisans';
```
</details>

### description
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="twitter:description" content="Laravel is a PHP web application framework with expressive, elegant syntax.">
```

```php
$meta->twitter->description === 'Laravel is a PHP web application framework with expressive, elegant syntax.';
```
</details>

### image
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<meta name="twitter:image" content="/image.jpg">
```

```php
$meta->twitter->image === 'https://example.com/image.jpg';
```
</details>

### imageAlt
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="twitter:image:alt" content="A description of the image">
```

```php
$meta->twitter->imageAlt === 'A description of the image';
```
</details>

### creator
`string` `null`

<details>
<summary>Example</summary>

```html
<meta name="twitter:creator" content="@author">
```

```php
$meta->twitter->creator === '@author';
```
</details>

### other
`array<string>`

<details>
<summary>Example</summary>

```html
<meta name="twitter:app:id:iphone" content="123456789">
```

```php
$meta->twitter->other === ['twitter:app:id:iphone' => '123456789'];
```
</details>

<a name="icon-object"><h2>Icon object</h2></a>

### url
`string`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<link rel="shortcut icon" href="favicon.ico" sizes="16x14">
```

```php
$icon->url === 'https://example.com/favicon.ico';
```
</details>

### mime
`string` `null`

> [!NOTE]
> The mime type will be set automatically based on the file extension if the "type" attribute is not specified.

<details>
<summary>Example</summary>

```html
<link rel="shortcut icon" href="favicon.ico" sizes="16x14">
```

```php
$icon->mime === 'application/ico';
```
</details>

### extension
`string` `null`

<details>
<summary>Example</summary>

```html
<link rel="shortcut icon" href="favicon.ico" sizes="16x14">
```

```php
$icon->extension === 'ico';
```
</details>

### sizes
`string` `null`

<details>
<summary>Example</summary>

```html
<link rel="shortcut icon" href="favicon.ico" sizes="16x14">
```

```php
$icon->sizes === '16x14';
```
</details>

### width
`int` `string` `null`

The property is computed from the "sizes" attribute (First number before "**x**").

> [!NOTE]
> The property will try to converse the value to `int`. In case of an incorrect value, `null` will be written. You can disable this behavior with the `dontUseTypeConversions` function (See [configuration](/README.md#config)).

<details>
<summary>Example</summary>

```html
<link rel="shortcut icon" href="favicon.ico" sizes="16x14">
```

```php
$icon->width === 16;
```
</details>

### height
`int` `string` `null`

The property is computed from the "sizes" attribute (Last number after "**x**").

> [!NOTE]
> The property will try to converse the value to `int`. In case of an incorrect value, `null` will be written. You can disable this behavior with the `dontUseTypeConversions` function (See [configuration](/README.md#config)).

<details>
<summary>Example</summary>

```html
<link rel="shortcut icon" href="favicon.ico" sizes="16x14">
```

```php
$icon->height === 14;
```
</details>

<a name="og-image-object"><h2>OpenGraph\Image object</h2></a>

### url
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<meta property="og:image" content="/image.jpg">
```

```php
$icon->url === 'https://example.com/image.jpg';
```
</details>

### secureUrl
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<meta property="og:image:secure_url" content="https://example.com/image.jpg">
```

```php
$icon->secureUrl === 'https://example.com/image.jpg';
```
</details>

### type
`string` `null`

> [!NOTE]
> If the type is not explicitly specified via the meta tag "og:image:type", it will be guessed based on the file extension.

<details>
<summary>Example</summary>

```html
<meta property="og:image:type" content="image/jpeg">
```

```php
$icon->type === 'image/jpeg';
```
</details>

### width
`int` `string` `null`

> [!NOTE]
> The property will try to converse the value to `int`. In case of an incorrect value, `null` will be written. You can disable this behavior with the `dontUseTypeConversions` function (See [configuration](/README.md#config)).

<details>
<summary>Example</summary>

```html
<meta property="og:image:width" content="1200">
```

```php
$icon->width === 1200;
```
</details>

### height
`int` `string` `null`

> [!NOTE]
> The property will try to converse the value to `int`. In case of an incorrect value, `null` will be written. You can disable this behavior with the `dontUseTypeConversions` function (See [configuration](/README.md#config)).

<details>
<summary>Example</summary>

```html
<meta property="og:image:height" content="630">
```

```php
$icon->height === 630;
```
</details>

### alt
`string` `null`

<details>
<summary>Example</summary>

```html
<meta property="og:image:alt" content="Image description">
```

```php
$icon->alt === 'Image description';
```
</details>

<a name="og-video-object"><h2>OpenGraph\Video object</h2></a>

### url
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<meta property="og:video" content="/video.mp4">
```

```php
$video->url === 'https://example.com/video.mp4';
```
</details>

### secureUrl
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<meta property="og:video:secure_url" content="https://example.com/video.mp4">
```

```php
$video->secureUrl === 'https://example.com/video.mp4';
```
</details>

### type
`string` `null`

> [!NOTE]
> If the type is not explicitly specified via the meta tag "og:video:type", it will be guessed based on the file extension.

<details>
<summary>Example</summary>

```html
<meta property="og:video:type" content="video/mp4">
```

```php
$video->type === 'video/mp4';
```
</details>

### width
`int` `string` `null`

> [!NOTE]
> The property will try to converse the value to `int`. In case of an incorrect value, `null` will be written. You can disable this behavior with the `dontUseTypeConversions` function (See [configuration](/README.md#config)).

<details>
<summary>Example</summary>

```html
<meta property="og:video:width" content="1200">
```

```php
$video->width === 1200;
```
</details>

### height
`int` `string` `null`

> [!NOTE]
> The property will try to converse the value to `int`. In case of an incorrect value, `null` will be written. You can disable this behavior with the `dontUseTypeConversions` function (See [configuration](/README.md#config)).

<details>
<summary>Example</summary>

```html
<meta property="og:video:height" content="630">
```

```php
$video->height === 630;
```
</details>

<a name="og-audio-object"><h2>OpenGraph\Audio object</h2></a>

### url
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<meta property="og:audio" content="/audio.mp3">
```

```php
$audio->url === 'https://example.com/audio.mp3';
```
</details>

### secureUrl
`string` `null`

> [!NOTE]
> The property process as a URL if not disabled via the `dontProcessUrls` setting.

<details>
<summary>Example</summary>

```html
<meta property="og:audio:secure_url" content="https://example.com/audio.mp3">
```

```php
$audio->secureUrl === 'https://example.com/audio.mp3';
```
</details>

### type
`string` `null`

> [!NOTE]
> If the type is not explicitly specified via the meta tag "og:audio:type", it will be guessed based on the file extension.

<details>
<summary>Example</summary>

```html
<meta property="og:audio:type" content="audio/mpeg">
```

```php
$audio->type === 'audio/mpeg';
```
</details>
