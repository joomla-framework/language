<?php

/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Language;
use Joomla\Language\LanguageFactory;
use Joomla\Language\LanguageHelper;
use Joomla\Language\MessageCatalogue;
use Joomla\Language\Parser\IniParser;
use Joomla\Language\ParserRegistry;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Language\Language.
 */
#[CoversClass(Language::class)]
#[UsesClass(IniParser::class)]
#[UsesClass(LanguageFactory::class)]
#[UsesClass(LanguageHelper::class)]
#[UsesClass(MessageCatalogue::class)]
#[UsesClass(ParserRegistry::class)]
class LanguageTest extends TestCase
{
    /**
     * Test language object
     *
     * @var  Language
     */
    protected $object;

    /**
     * File loader registry
     *
     * @var  ParserRegistry
     */
    protected $parserRegistry;

    /**
     * Path to language folder used for testing
     *
     * @var  string
     */
    private $testPath;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->parserRegistry = new ParserRegistry();
        $this->parserRegistry->add(new IniParser());

        $this->testPath = __DIR__ . '/data';
        $this->object   = new Language($this->parserRegistry, $this->testPath, 'en-GB');
        $this->object->load();
    }

    #[TestDox('Verify that Language is instantiated correctly')]
    public function testVerifyThatLanguageIsInstantiatedCorrectly()
    {
        $this->assertInstanceOf(Language::class, new Language($this->parserRegistry, $this->testPath));
    }

    #[TestDox('Verify that Language::translate() returns an empty string when one is input')]
    public function testTranslateReturnsEmptyStringWhenGivenAnEmptyString()
    {
        $this->assertEmpty($this->object->translate(''));
    }

    #[TestDox('Verify that Language::translate() returns the correct string for a key')]
    public function testTranslateReturnsTheCorrectStringForAKey()
    {
        $this->assertSame('Bar', $this->object->translate('FOO'));
    }

    #[TestDox('Verify that Language::translate() returns the correct string for a key in debug mode')]
    public function testTranslateReturnsTheCorrectStringForAKeyInDebugMode()
    {
        $this->object->setDebug(true);
        $this->assertSame('**Bar**', $this->object->translate('FOO'));
    }

    #[TestDox('Verify that Language::translate() identifies a key as unknown in debug mode')]
    public function testTranslateIdentifiesAKeyAsUnknownInDebugMode()
    {
        $this->object->setDebug(true);
        $this->assertSame('??BAR??', $this->object->translate('BAR'));
    }

    #[TestDox('Verify that Language::translate() returns a JavaScript safe string')]
    public function testTranslateReturnsAJavascriptSafeKey()
    {
        $this->assertSame('foobar\\\'s', $this->object->translate('foobar\'s', true));
    }

    #[TestDox('Verify that Language::translate() returns a string without backslashes interpreted')]
    public function testTranslateReturnsAStringWithoutBackslashesInterpreted()
    {
        $this->assertSame('foobar\\\'s', $this->object->translate('foobar\'s', true, false));
    }

    #[TestDox('Verify that Language::transliterate() calls defined transliterator')]
    public function testTransliterateCallsDefinedTransliterator()
    {
        $this->assertSame('Así', $this->object->transliterate('Así'));
    }

    #[TestDox('Verify that Language::getPluralSuffixes() calls the defined method')]
    public function testGetPluralSuffixesCallsTheDefinedMethod()
    {
        $this->assertIsArray($this->object->getPluralSuffixes(1));
    }

    #[TestDox('Verify that Language::load() successfully loads the main language file')]
    public function testVerifyLoadSuccessfullyLoadsTheMainLanguageFile()
    {
        $this->assertTrue($this->object->load());
    }

    #[TestDox('Verify that Language::load() successfully loads a language file')]
    public function testVerifyLoadSuccessfullyLoadsALanguageFile()
    {
        $this->assertTrue($this->object->load('good'));
    }

    #[TestDox('Verify that Language::load() fails to load an extension language file with errors')]
    public function testVerifyLoadFailsToLoadAnExtensionLanguageFileWithErrors()
    {
        $this->assertFalse($this->object->load('bad'));
    }

    #[TestDox('Verify that Language::load() successfully loads a language file')]
    public function testVerifyLoadLanguageSuccessfullyLoadsALanguageFile()
    {
        $this->assertTrue(TestHelper::invoke($this->object, 'loadLanguage', $this->testPath . '/good.ini'));
    }

    #[TestDox('Verify that Language::parse() successfully parses a language file')]
    public function testVerifyParseSuccessfullyParsesALanguageFile()
    {
        $this->assertNotEmpty(TestHelper::invoke($this->object, 'parse', $this->testPath . '/good.ini'));
    }

    #[TestDox('Verify that Language::parse() successfully parses a language file in debug mode')]
    public function testVerifyParseSuccessfullyParsesALanguageFileInDebugMode()
    {
        $this->object->setDebug(true);

        $this->assertNotEmpty(TestHelper::invoke($this->object, 'parse', $this->testPath . '/good.ini'));
    }

    #[TestDox('Verify that Language::parse() fails to parse a language file with errors')]
    public function testVerifyParseFailsToParseALanguageFileWithErrors()
    {
        $this->assertEmpty(TestHelper::invoke($this->object, 'parse', $this->testPath . '/bad.ini'));
    }

    #[TestDox('Verify that Language::parse() fails to parse a language file with errors in debug mode')]
    public function testVerifyParseFailsToParseALanguageFileWithErrorsInDebugMode()
    {
        $this->object->setDebug(true);

        $this->assertEmpty(TestHelper::invoke($this->object, 'parse', $this->testPath . '/bad.ini'));
    }

    #[TestDox('Verify that Language::debugFile() finds no errors in a good file')]
    public function testVerifyDebugFileFindsNoErrorsInAGoodFile()
    {
        $this->assertSame(0, $this->object->debugFile($this->testPath . '/good.ini'));
    }

    #[TestDox('Verify that Language::debugFile() finds errors in a bad file')]
    public function testVerifyDebugFileFindsErrorsInABadFile()
    {
        $this->assertGreaterThan(0, $this->object->debugFile($this->testPath . '/bad.ini'));
    }

    #[TestDox('Verify that Language::get() returns the correct metadata')]
    public function testVerifyThatGetReturnsTheCorrectMetadata()
    {
        $this->assertEquals('en-GB', $this->object->get('tag'));
    }

    #[TestDox('Verify that Language::get() returns the default if metadata does not exist')]
    public function testVerifyThatGetReturnsTheDefaultIfMetadataDoesNotExist()
    {
        $this->assertEquals('default', $this->object->get('doesnotexist', 'default'));
    }

    #[TestDox('Verify that Language::getBasePath() returns the correct path')]
    public function testVerifyThatGetBasePathReturnsTheCorrectPath()
    {
        $this->assertSame($this->testPath, $this->object->getBasePath());
    }

    #[TestDox('Verify that Language::getCallerInfo() returns an array')]
    public function testVerifyGetCallerInfoReturnsAnArray()
    {
        $this->assertIsArray(TestHelper::invoke($this->object, 'getCallerInfo'));
    }

    #[TestDox('Verify that Language::getName() returns the correct metadata')]
    public function testVerifyThatGetNameReturnsTheCorrectMetadata()
    {
        $this->assertSame('English (United Kingdom)', $this->object->getName());
    }

    #[TestDox('Verify that Language::getPaths() default returns an array')]
    public function testVerifyThatGetPathsDefaultReturnsAnArray()
    {
        $this->assertIsArray($this->object->getPaths());
    }

    #[TestDox('Verify that Language::getPaths() returns null for an unloaded extension')]
    public function testVerifyThatGetPathsReturnsNullForAnUnloadedExtension()
    {
        $this->assertNull($this->object->getPaths('good'));
    }

    #[TestDox('Verify that Language::getPaths() returns the extension path for a loaded extension')]
    public function testVerifyThatGetPathsReturnsTheExtensionPathForALoadedExtension()
    {
        $this->object->load('good');

        $this->assertIsArray($this->object->getPaths('good'));
    }

    #[TestDox('Verify that Language::getErrorFiles() default returns an array')]
    public function testVerifyThatGetErrorFilesDefaultReturnsAnArray()
    {
        $this->assertIsArray($this->object->getErrorFiles());
    }

    #[TestDox('Verify that Language::getTag() returns the correct metadata')]
    public function testVerifyThatGetTagReturnsTheCorrectMetadata()
    {
        $this->assertSame('en-GB', $this->object->getTag());
    }

    #[TestDox('Verify that Language::isRTL() default returns false')]
    public function testVerifyThatIsRTLDefaultReturnsFalse()
    {
        $this->assertFalse($this->object->isRTL());
    }

    #[TestDox('Verify that Language::setDebug() returns the previous debug state')]
    public function testVerifyThatSetDebugReturnsThePreviousDebugState()
    {
        $this->assertFalse($this->object->setDebug(true));
    }

    #[TestDox('Verify that Language::getDebug() default returns false')]
    public function testVerifyThatGetDebugDefaultReturnsFalse()
    {
        $this->assertFalse($this->object->getDebug());
    }

    #[TestDox('Verify that Language::setDefault() returns the previous default language')]
    public function testVerifyThatSetDefaultReturnsThePreviousDefaultLanguage()
    {
        $this->assertSame('en-GB', $this->object->setDefault('de-DE'));
    }

    #[TestDox("Verify that Language::getDefault() default returns 'en-GB'")]
    public function testVerifyTheDefaultReturnForGetDefault()
    {
        $this->assertSame('en-GB', $this->object->getDefault());
    }

    #[TestDox('Verify that Language::getOrphans() default returns an array')]
    public function testVerifyThatGetOrphansDefaultReturnsAnArray()
    {
        $this->assertIsArray($this->object->getOrphans());
    }

    #[TestDox('Verify that Language::getUsed() default returns an array')]
    public function testVerifyThatGetUsedDefaultReturnsAnArray()
    {
        $this->assertIsArray($this->object->getUsed());
    }

    #[TestDox('Verify that Language::hasKey() returns false for a non-existing language key')]
    public function testVerifyThatHasKeyReturnsFalseForANonExistingLanguageKey()
    {
        $this->assertFalse($this->object->hasKey('com_admin.key'));
    }

    #[TestDox("Verify that Language::getLanguage() default returns 'en-GB'")]
    public function testVerifyTheDefaultReturnForGetLanguage()
    {
        $this->assertSame('en-GB', $this->object->getLanguage());
    }

    #[TestDox('Verify that Language::getLocale() default returns an array')]
    public function testVerifyTheDefaultReturnForGetLocale()
    {
        $this->assertIsArray($this->object->getLocale());
    }

    #[TestDox('Verify that Language::getFirstDay() default returns an integer for the first day of the week')]
    public function testVerifyTheDefaultReturnForGetFirstDay()
    {
        $this->assertSame(0, $this->object->getFirstDay());
    }

    #[TestDox('Verify that Language::getWeekEnd() default returns an array')]
    public function testVerifyTheDefaultReturnForGetWeekEnd()
    {
        $this->assertSame('0,6', $this->object->getWeekEnd());
    }
}
