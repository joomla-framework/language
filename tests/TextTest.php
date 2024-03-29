<?php

/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\LanguageFactory;
use Joomla\Language\Parser\IniParser;
use Joomla\Language\ParserRegistry;
use Joomla\Language\Text;
use Joomla\Language\Language;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Language\Text.
 */
class TextTest extends TestCase
{
    /**
     * LanguageFactory object to use for testing
     *
     * @var  LanguageFactory
     */
    private static $factory;

    /**
     * Test Text object
     *
     * @var  Text
     */
    protected $object;

    /**
     * File parser registry
     *
     * @var  ParserRegistry
     */
    private $parserRegistry;

    /**
     * Path to language folder used for testing
     *
     * @var  string
     */
    private static $testPath;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        self::$testPath = __DIR__ . '/data';

        self::$factory = new LanguageFactory();
        self::$factory->setLanguageDirectory(self::$testPath);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->parserRegistry = new ParserRegistry();
        $this->parserRegistry->add(new IniParser());

        $language = new Language($this->parserRegistry, self::$testPath, 'en-GB');
        $language->load();
        $this->object = new Text($language);
    }

    /**
     * @testdox  Verify that Text is instantiated correctly
     *
     * @covers   Joomla\Language\Text
     *
     * @doesNotPerformAssertions
     */
    public function testVerifyThatTextIsInstantiatedCorrectly()
    {
        new Text(new Language($this->parserRegistry, self::$testPath));
    }

    /**
     * @testdox  Verify that the Language object can be managed
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testSetGetLanguage()
    {
        $language = new Language($this->parserRegistry, self::$testPath, 'de-DE');

        $this->assertSame($this->object, $this->object->setLanguage($language), 'The setLanguage method has a fluent interface');
        $this->assertSame($language, $this->object->getLanguage());
    }

    /**
     * @testdox  Verify that Text::translate() returns an empty string when one is input
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testTranslateReturnsEmptyStringWhenGivenAnEmptyString()
    {
        $this->assertEmpty($this->object->translate(''));
    }

    /**
     * @testdox  Verify that Text::translate() returns the correct string for a key
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testTranslateReturnsTheCorrectStringForAKey()
    {
        $this->assertSame('Bar', $this->object->translate('Bar'));
    }

    /**
     * @testdox  Verify that Text::translate() returns the correct string for a key with named parameters
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testTranslateReturnsTheCorrectStringForAKeyWithNamedParameters()
    {
        $this->assertSame('Bar None', $this->object->translate('Bar %value%', ['%value%' => 'None']));
    }

    /**
     * @testdox  Verify that Text::translate() returns a JavaScript safe string
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testTranslateReturnsAJavascriptSafeKey()
    {
        $this->assertSame('foobar\\\'s', $this->object->translate('foobar\'s', [], true));
    }

    /**
     * @testdox  Verify that Text::alt() returns the correct string for a key with no alt
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testAltReturnsTheCorrectStringForAKey()
    {
        $this->assertSame('Bar', $this->object->alt('FOO', ''));
    }

    /**
     * @testdox  Verify that Text::alt() returns the correct string for a key with an alt
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testAltReturnsTheCorrectStringForAKeyWithAlt()
    {
        $this->assertSame('Car', $this->object->alt('FOO', 'GOO'));
    }

    /**
     * @testdox  Verify that Text::alt() returns the correct string for a key with an alt and named parameters
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testAltReturnsTheCorrectStringForAKeyWithAltAndNamedParameters()
    {
        $this->assertSame('Green Car', $this->object->alt('FOO', 'BOO', ['%description%' => 'Green']));
    }

    /**
     * @testdox  Verify that Text::plural() returns the input key when no plural key is found
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testPluralReturnsInputKeyWhenNoParamsPassed()
    {
        $this->assertSame('BAR', $this->object->plural('BAR', 0));
    }

    /**
     * @testdox  Verify that Text::plural() returns the translated string when the pluralised key is found
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testPluralReturnsTranslatedStringWhenPluralisedKeyFound()
    {
        $this->assertSame('3 Bars', $this->object->plural('BAR', 3));
    }

    /**
     * @testdox  Verify that Text::sprintf() returns the input key when no key is found
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testSprintfReturnsInputKeyWhenKeyNotFound()
    {
        $this->assertSame('BAR_NONE', $this->object->sprintf('BAR_NONE', 0));
    }

    /**
     * @testdox  Verify that Text::sprintf() returns the translated string when the specified key is found
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testSprintfReturnsTranslatedStringWhenKeyFound()
    {
        $this->assertSame('I have 3 cars!', $this->object->sprintf('MANY_CARS', 3));
    }

    /**
     * @testdox  Verify that Text::printf() returns the input key when no key is found
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testPrintfReturnsEmptyStringWhenKeyNotFound()
    {
        ob_start();
        $this->object->printf('BAR_NONE', 0);
        $return = ob_get_clean();

        $this->assertSame('BAR_NONE', $return);
    }

    /**
     * @testdox  Verify that Text::printf() returns the translated string when the specified key is found
     *
     * @covers   Joomla\Language\Text
     * @uses     Joomla\Language\Language
     * @uses     Joomla\Language\LanguageFactory
     * @uses     Joomla\Language\LanguageHelper
     * @uses     Joomla\Language\MessageCatalogue
     * @uses     Joomla\Language\ParserRegistry
     * @uses     Joomla\Language\Parser\IniParser
     */
    public function testPrintfReturnsTranslatedStringWhenKeyFound()
    {
        ob_start();
        $this->object->printf('MANY_CARS', 3);
        $return = ob_get_clean();

        $this->assertSame('I have 3 cars!', $return);
    }
}
