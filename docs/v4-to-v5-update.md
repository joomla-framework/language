# Updating from v4 to v5

Release 5.0.0 removes the Porter stemmer that was deprecated in 4.0.

## At a glance

| | v4 (4.x) | v5 (5.0.0) |
|---|---|---|
| `Stemmer\Porteren`, `LanguageFactory::getStemmer()`, `StemmerInterface` | deprecated, work | **removed** |

## The bundled stemmer has been removed

Deprecated in 4.0, removed in 5.0:

| Removed | Use instead |
|---|---|
| `Joomla\Language\Stemmer\Porteren` | [`wamania/php-stemmer`](https://github.com/wamania/php-stemmer) |
| `LanguageFactory::getStemmer()` | `Wamania\Snowball\StemmerFactory::create()` |
| `Joomla\Language\StemmerInterface` | `Wamania\Snowball\Stemmer\Stemmer`, or your own interface |

```bash
composer require wamania/php-stemmer
```

```php
// Before
$stemmer = $languageFactory->getStemmer('porteren');
$root    = $stemmer->stem('walking', 'en');

// After
use Wamania\Snowball\StemmerFactory;

$stemmer = StemmerFactory::create('en');
$root    = $stemmer->stem('walking');
```

`wamania/php-stemmer` supports more languages than English. Its stemmers do not take a language
argument in `stem()`; the language is chosen when the stemmer is created.

To find the call sites:

```bash
grep -rnE -- 'getStemmer\(|Stemmer\\Porteren|StemmerInterface' src/
```
