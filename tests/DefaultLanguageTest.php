<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Language;
use Joomla\Language\LanguageFactory;
use Joomla\Language\LanguageHelper;
use Joomla\Language\MessageCatalogue;
use Joomla\Language\Parser\IniParser;
use Joomla\Language\ParserRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests that a Language created without a language tag behaves like one created with the default tag.
 */
#[CoversClass(Language::class)]
#[UsesClass(LanguageFactory::class)]
#[UsesClass(LanguageHelper::class)]
#[UsesClass(MessageCatalogue::class)]
#[UsesClass(ParserRegistry::class)]
#[UsesClass(IniParser::class)]
class DefaultLanguageTest extends TestCase
{
    private function createLanguage(string $lang = ''): Language
    {
        $registry = new ParserRegistry();
        $registry->add(new IniParser());

        return new Language($registry, __DIR__ . '/data', $lang);
    }

    #[TestDox('Language without a tag loads the override file of the default language')]
    public function testDefaultLanguageLoadsOverrides(): void
    {
        $explicit = $this->createLanguage('en-GB');
        $default  = $this->createLanguage();

        $this->assertSame('en-GB', $default->getLanguage());
        $this->assertSame('Ride', $explicit->translate('OVER'));
        $this->assertSame('Ride', $default->translate('OVER'));
    }

    #[TestDox('Language without a tag uses the localise class of the default language')]
    public function testDefaultLanguageUsesLocalise(): void
    {
        // tests/data/language/en-GB/en-GB.localise.php returns ['MORE'] for counts above one
        $this->assertSame(['MORE'], $this->createLanguage()->getPluralSuffixes(5));
    }
}
