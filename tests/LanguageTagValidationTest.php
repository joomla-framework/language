<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Language;
use Joomla\Language\LanguageFactory;
use Joomla\Language\LanguageHelper;
use Joomla\Language\Parser\IniParser;
use Joomla\Language\ParserRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests that language tags and extension names cannot be used to leave the language folder.
 */
#[CoversClass(Language::class)]
#[CoversClass(LanguageFactory::class)]
#[CoversClass(LanguageHelper::class)]
#[UsesClass(ParserRegistry::class)]
#[UsesClass(IniParser::class)]
class LanguageTagValidationTest extends TestCase
{
    /**
     * Path to the test data
     *
     * @var  string
     */
    private string $testPath = __DIR__ . '/data';

    public static function validTagProvider(): \Generator
    {
        yield 'language and region' => ['en-GB'];
        yield 'three letter language' => ['ast-ES'];
        yield 'language only' => ['de'];
        yield 'with script' => ['sr-Latn-RS'];
        yield 'numeric region' => ['es-419'];
    }

    public static function invalidTagProvider(): \Generator
    {
        yield 'parent directory' => ['../../tmp/evil'];
        yield 'parent directory only' => ['..'];
        yield 'absolute path' => ['/etc/passwd'];
        yield 'windows path' => ['C:' . \chr(92) . 'evil'];
        yield 'directory separator' => ['en-GB/../xx-XX'];
        yield 'null byte' => ["en-GB\0"];
        yield 'trailing newline' => ["en-GB\n"];
        yield 'class name characters' => ['Foo' . \chr(92) . 'Bar'];
        yield 'uppercase primary subtag' => ['EN-GB'];
        yield 'underscore' => ['en_GB'];
    }

    private function createParserRegistry(): ParserRegistry
    {
        $registry = new ParserRegistry();
        $registry->add(new IniParser());

        return $registry;
    }

    #[TestDox('LanguageHelper::isValidLanguageTag() accepts well-formed tags')]
    #[DataProvider('validTagProvider')]
    public function testValidTagsAreAccepted(string $tag): void
    {
        $this->assertTrue((new LanguageHelper())->isValidLanguageTag($tag));
    }

    #[TestDox('LanguageHelper::isValidLanguageTag() rejects malformed tags')]
    #[DataProvider('invalidTagProvider')]
    public function testInvalidTagsAreRejected(string $tag): void
    {
        $this->assertFalse((new LanguageHelper())->isValidLanguageTag($tag));
    }

    #[TestDox('Language cannot be constructed with an invalid language tag')]
    #[DataProvider('invalidTagProvider')]
    public function testConstructorRejectsInvalidTag(string $tag): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Language($this->createParserRegistry(), $this->testPath, $tag);
    }

    #[TestDox('Language::load() rejects an invalid language tag')]
    #[DataProvider('invalidTagProvider')]
    public function testLoadRejectsInvalidTag(string $tag): void
    {
        $language = new Language($this->createParserRegistry(), $this->testPath, 'en-GB');

        $this->expectException(\InvalidArgumentException::class);

        $language->load('joomla', '', $tag);
    }

    #[TestDox('Language::load() rejects an extension name that leaves the language folder')]
    public function testLoadRejectsInvalidExtension(): void
    {
        $language = new Language($this->createParserRegistry(), $this->testPath, 'en-GB');

        $this->expectException(\InvalidArgumentException::class);

        $language->load('../../en-GB/en-GB');
    }

    #[TestDox('Language::load() still accepts regular extension names')]
    public function testLoadAcceptsRegularExtension(): void
    {
        $language = new Language($this->createParserRegistry(), $this->testPath, 'en-GB');

        $this->assertTrue($language->load('good'));
        $this->assertFalse($language->load('com_does_not_exist'));
    }

    #[TestDox('LanguageFactory::getLocalise() does not include files for an invalid language tag')]
    public function testGetLocaliseRejectsInvalidTag(): void
    {
        // Points at tests/data/language/xx-XX/xx-XX.localise.php through the overrides folder
        $this->expectException(\InvalidArgumentException::class);

        (new LanguageFactory())->getLocalise('../xx-XX/xx-XX', $this->testPath);
    }

    #[TestDox('LanguageHelper::exists() and getMetadata() treat an invalid tag as a missing language')]
    public function testHelperTreatsInvalidTagAsMissing(): void
    {
        $helper = new LanguageHelper();

        // The en-GB folder exists, but must not be reachable through a path
        $this->assertFalse($helper->exists('xx-XX/../en-GB', $this->testPath));
        $this->assertNull($helper->getMetadata('xx-XX/../en-GB', $this->testPath));
    }

    #[TestDox('LanguageHelper::getLanguagePath() rejects an invalid language tag')]
    public function testGetLanguagePathRejectsInvalidTag(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new LanguageHelper())->getLanguagePath($this->testPath, '../../etc');
    }
}
