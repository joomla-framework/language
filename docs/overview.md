## Overview

The Language package provides an interface for managing internationalisation support and translations within an application.
The base objects in the package, `Language` and `Text` are the primary classes used for configuring your application's
language data and translating language keys. Additionally, support for [stemming](http://en.wikipedia.org/wiki/Stemming)
and [transliteration](http://en.wikipedia.org/wiki/Transliteration) is also available via the `Stemmer` and `Transliterate` classes.

### Language Package Structure

A language package contains data to configure and customise the language handling and translations for a given language.
The base package requires the following files (note that `xx-XX` is used to represent a language code):

- `xx-XX.ini` - The base language strings for an application.
- `xx-XX.localise.php` - A localisation class to define language specific behaviours. This class must implement the `LocaliseInterface`.
- `xx-XX.xml` - The metadata for a language.

The base path to language packages must be specified when instantiating a `Language` instance. Language packages must be
stored in a `language` folder within this base path, and each language stored in a separate folder by langauge code. For example:

```php
use Joomla\Language\Language;

$language = new Language('/var/www/jfw-application', 'en-GB');
```

This will create a `Language` instance for the `en-GB` language and specifies that the base folder path is `/var/www/jfw-application`.
Therefore, the XML metadata file should be stored to `/var/www/jfw-application/language/en-GB/en-GB.xml`.

### Translating Language Keys

The `Text` class is responsible for translating language keys into a human friendly text string in the requested language.
Instantiating a `Text` class requires a `Language` instance is injected in the constructor. For convenience, a `Text` instance
can be retrieved via `Language::getText()`. More information about using the `Text` class can be found in the
[class documentation](classes/Text.md).

### Language Key Format

In order to be properly translated, language keys must match a specific format. Generally, a key must match the
`#^[A-Z][A-Z0-9_\-\.]*\s*` regex. Broken down, the first character of the key must be a letter, then any letter or number
are considered valid characters as well as an underscore (`_`), hyphen (`-`), or period (`.`). Only uppercase letters
are accepted. A language file can be debugged using the `Language::debugFile()` method. If `debugFile()` indicates there
are errors, the error list can be retrieved via `Language::getErrorFiles()`.

```php
use Joomla\Language\Language;

$language = new Language('/var/www/jfw-application', 'en-GB');

if (count($language->debugFile('/var/www/jfw-application/language/en-GB/en-GB.ini'))
{
	$errors = $language->getErrorFiles();

	// Application logic to display errors
}
```

## Things to know before you build on this

**The language tag becomes part of a file path without validation.** `LanguageHelper::getLanguagePath()`
concatenates the tag onto the base path, and `Language::load()` builds
`"$path/$lang.$extension.ini"` from it. If the tag comes from a request parameter — the usual way
to switch languages — validate it before it reaches the package:

```php
$lang = $input->getCmd('lang', 'en-GB');

if (!preg_match('/^[a-z]{2,3}-[A-Z]{2}$/', $lang)) {
    $lang = 'en-GB';
}
```

The same applies to the `$extension` argument of `load()`.

**`Text::sprintf()` uses the translation as the format string.** A translation containing more
placeholders than you pass raises an `ArgumentCountError`, and one containing `%1$s` can reach
arguments you did not intend to show. Translations often come from third-party language packs, so
treat them as input you do not fully control:

```php
// The format string here is whatever the .ini file says
Text::sprintf('COM_EXAMPLE_GREETING', $name);
```

**Translations are not escaped.** `translate()` returns the raw string; only optional JavaScript
escaping and backslash interpretation are applied. Language files may legitimately contain HTML, so
escaping is the caller's decision — make it deliberately.

**A failed parse reports the file name and the error the wrong way round.** `IniParser::loadFile()`
builds its exception message with the arguments swapped, so the message reads
`Could not process file <error>: <path>`. Read it accordingly until it is fixed.

**Keys not matching `[A-Z][A-Z0-9_*.-]*` are dropped silently.** The INI parser validates keys and
skips anything else — including lower-case keys — without a warning. The same happens for lines
without `=` and for multi-dimensional array keys.

**`OutputFilter::setLanguage()` stores the instance statically.** `Joomla\Filter\OutputFilter`
keeps whatever language you hand it for the rest of the process, which is worth knowing when more
than one language is in play.
