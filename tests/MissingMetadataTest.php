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
 * Tests the metadata getters of Joomla\Language\Language for a language without a metadata file.
 */
#[CoversClass(Language::class)]
#[UsesClass(LanguageFactory::class)]
#[UsesClass(LanguageHelper::class)]
#[UsesClass(MessageCatalogue::class)]
#[UsesClass(ParserRegistry::class)]
#[UsesClass(IniParser::class)]
class MissingMetadataTest extends TestCase
{
    /**
     * Language without a metadata file (there is no tests/data/language/de-DE folder)
     *
     * @var  Language
     */
    private Language $language;

    /**
     * Warnings raised during the test
     *
     * @var  string[]
     */
    private array $warnings = [];

    protected function setUp(): void
    {
        parent::setUp();

        $registry = new ParserRegistry();
        $registry->add(new IniParser());

        $this->language = new Language($registry, __DIR__ . '/data', 'de-DE');

        set_error_handler(
            function (int $errno, string $errstr): bool {
                $this->warnings[] = $errstr;

                return true;
            }
        );
    }

    protected function tearDown(): void
    {
        restore_error_handler();

        parent::tearDown();
    }

    #[TestDox('Language::getName() returns null without a warning when the metadata is missing')]
    public function testGetName(): void
    {
        $this->assertNull($this->language->getName());
        $this->assertSame([], $this->warnings);
    }

    #[TestDox('Language::getTag() returns null without a warning when the metadata is missing')]
    public function testGetTag(): void
    {
        $this->assertNull($this->language->getTag());
        $this->assertSame([], $this->warnings);
    }

    #[TestDox('Language::isRtl() returns false without a warning when the metadata is missing')]
    public function testIsRtl(): void
    {
        $this->assertFalse($this->language->isRtl());
        $this->assertSame([], $this->warnings);
    }
}
