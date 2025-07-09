## Updating from v3 to v4

The following changes were made to the Language package between v3 and v4.

### Minimum supported PHP version raised

All Framework packages now require PHP 8.3 or newer.

### `Joomla\Language\Stemmer\Porteren` has been deprecated

The english Porter stemmer included in this package has been deprecated. Instead we suggest using [`wamania/php-stemmer`](https://github.com/wamania/php-stemmer), which supports more than just english. At the same time, `LanguageFactory::getStemmer()` has also been deprecated. The stemmer included in this package will be removed in version 5.0.

### Several methods from `Joomla\Language\Language` have been removed

The following methods have been deprecated in version 2.0 and now been removed in 4.0:

* `Language::_()`: Use `Language::translate()` instead.
* `Language::exists()`: Use `LanguageHelper::exists()` instead.
* `Language::getMetadata()`: Use `LanguageHelper::getMetadata()` instead.
* `Language::getKnownLanguages()`: Use `LanguageHelper::getKnownLanguages()` instead.
* `Language::getLanguagePath()`: Use `LanguageHelper::getLanguagePath()` instead.
* `Language::parseLanguageFiles()`: Use `LanguageHelper::parseLanguageFiles()` instead.
* `Language::parseXmlLanguageFile()`: Use `LanguageHelper::parseXmlLanguageFile()` instead.
