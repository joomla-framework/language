# Updating from v2 to v3

Release 3.0.0 raises the PHP requirement and reformats the codebase. **No public or protected
method signature changed**, so code written against 2.x keeps working on PHP 8.1.

## At a glance

| | v2 (2.0.x) | v3 (3.0.0) |
|---|---|---|
| PHP | `^7.2.5 \| ~8.0.0 \| ~8.1.0` | `^8.1.0` |
| Public API | — | unchanged |
| Coding style | Joomla Coding Standard | PSR-12 |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.1** or newer.

## No API changes

`Language`, `LanguageFactory`, `LanguageHelper`, `Text`, `MessageCatalogue`, the parsers and the
localise classes have the same signatures in 3.0.0 as in 2.0.0.

The seven `Language` methods deprecated in 2.0 are still present and still emit deprecation
notices. They were removed in 4.0.0 — see [Updating from v3 to v4](v3-to-v4-update.md). Fixing them
while on 3.x makes that upgrade a no-op.

## Codebase converted to PSR-12

The package was reformatted from the Joomla Coding Standard to PSR-12. This touches nearly every
line and changes no behaviour.

## Dependency changes

| Package | v2 (2.0.x) | v3 (3.0.0) |
|---|---|---|
| `php` | `^7.2.5 \| ~8.0.0 \| ~8.1.0` | `^8.1.0` |
| `joomla/string` | `^2.0` | `^3.0` |
| `symfony/deprecation-contracts` | `^2.1` | `^2 \| ^3` |
| `ext-xml` | `*` | unchanged |
