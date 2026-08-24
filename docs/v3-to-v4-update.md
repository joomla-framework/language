# Updating from v3 to v4

Release 4.0.0 raises the PHP requirement and removes seven `Language` methods that were deprecated
in 2.0. The bundled Porter stemmer is deprecated in turn.

## At a glance

| | v3 (3.0.1) | v4 (4.0.0) |
|---|---|---|
| PHP | `^8.1.0` | `^8.3.0` |
| Seven `Language` methods | deprecated, work | **removed** |
| `Stemmer\Porteren`, `LanguageFactory::getStemmer()` | supported | deprecated, removal in 5.0 |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.3** or newer.

## Several methods from `Joomla\Language\Language` have been removed

Deprecated in 2.0, removed in 4.0:

| Removed | Use instead |
|---|---|
| `Language::_()` | `Language::translate()` |
| `Language::exists()` | `LanguageHelper::exists()` |
| `Language::getMetadata()` | `LanguageHelper::getMetadata()` |
| `Language::getKnownLanguages()` | `LanguageHelper::getKnownLanguages()` |
| `Language::getLanguagePath()` | `LanguageHelper::getLanguagePath()` |
| `Language::parseLanguageFiles()` | `LanguageHelper::parseLanguageFiles()` |
| `Language::parseXmlLanguageFile()` | `LanguageHelper::parseXmlLanguageFile()` |

The six `LanguageHelper` replacements are **instance** methods, where the `Language` ones were
static, so they need a helper object. `Language` keeps one in a `protected $helper` property but
exposes no getter, so create your own:

```php
use Joomla\Language\LanguageHelper;

// v3
$exists = Language::exists('de-DE', $basePath);

// v4
$exists = (new LanguageHelper())->exists('de-DE', $basePath);
```

`LanguageHelper` holds no state, so a fresh instance is as good as any. If you call several of
these, keep one around — or inject it, which makes the code testable:

```php
final class LanguageInstaller
{
    public function __construct(private readonly LanguageHelper $helper) {}

    public function availableLanguages(string $basePath): array
    {
        return $this->helper->getKnownLanguages($basePath);
    }
}
```

Their signatures are also typed now, and stricter than the removed static ones:

```php
public function exists(string $lang, string $basePath): bool
public function getMetadata(string $lang, string $path): ?array
public function getKnownLanguages(string $basePath): array
public function getLanguagePath(string $basePath, string $language = ''): string
public function parseLanguageFiles(string $dir = ''): array
public function parseXmlLanguageFile(string $path): ?array
```

Note that `exists()`, `getMetadata()` and `getKnownLanguages()` require `$basePath` — the static
versions defaulted it to `''`.

`_()` is the exception — it was an instance method already, and `translate()` takes the same
arguments:

```php
// v3
$language->_('COM_EXAMPLE_LABEL', $jsSafe, $interpretBackSlashes);

// v4
$language->translate('COM_EXAMPLE_LABEL', $jsSafe, $interpretBackSlashes);
```

To find the call sites:

```bash
grep -rnE -- '->_\(|Language::(exists|getMetadata|getKnownLanguages|getLanguagePath|parseLanguageFiles|parseXmlLanguageFile)\(' src/
```

## `Joomla\Language\Stemmer\Porteren` has been deprecated

The English Porter stemmer bundled with this package is deprecated, as is
`LanguageFactory::getStemmer()`. Both will be removed in 5.0. Use
[`wamania/php-stemmer`](https://github.com/wamania/php-stemmer), which supports more than English:

```bash
composer require wamania/php-stemmer
```

```php
use Wamania\Snowball\StemmerFactory;

$stemmer = StemmerFactory::create('de');
$stemmer->stem('Häuser');
```

`StemmerInterface` remains, so an adapter around another library still fits where the bundled
stemmer did.

## Dependency changes

| Package | v3 (3.0.1) | v4 (4.0.0) |
|---|---|---|
| `php` | `^8.1.0` | `^8.3.0` |
| `joomla/string` | `^3.0` | `^4.0` |
| `ext-xml` | `*` | unchanged |
| `symfony/deprecation-contracts` | `^2 \| ^3` | unchanged |
