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
use Joomla\Language\Text;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \Joomla\Language\Text.
 */
#[CoversClass(Text::class)]
#[UsesClass(IniParser::class)]
#[UsesClass(Language::class)]
#[UsesClass(LanguageFactory::class)]
#[UsesClass(LanguageHelper::class)]
#[UsesClass(MessageCatalogue::class)]
#[UsesClass(ParserRegistry::class)]
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

    #[DoesNotPerformAssertions]
    #[TestDox('Verify that Text is instantiated correctly')]
    public function testVerifyThatTextIsInstantiatedCorrectly()
    {
        new Text(new Language($this->parserRegistry, self::$testPath));
    }

    #[TestDox('Verify that the Language object can be managed')]
    public function testSetGetLanguage()
    {
        $language = new Language($this->parserRegistry, self::$testPath, 'de-DE');

        $this->assertSame($this->object, $this->object->setLanguage($language), 'The setLanguage method has a fluent interface');
        $this->assertSame($language, $this->object->getLanguage());
    }

    #[TestDox('Verify that Text::translate() returns an empty string when one is input')]
    public function testTranslateReturnsEmptyStringWhenGivenAnEmptyString()
    {
        $this->assertEmpty($this->object->translate(''));
    }

    #[TestDox('Verify that Text::translate() returns the correct string for a key')]
    public function testTranslateReturnsTheCorrectStringForAKey()
    {
        $this->assertSame('Bar', $this->object->translate('Bar'));
    }

    #[TestDox('Verify that Text::translate() returns the correct string for a key with named parameters')]
    public function testTranslateReturnsTheCorrectStringForAKeyWithNamedParameters()
    {
        $this->assertSame('Bar None', $this->object->translate('Bar %value%', ['%value%' => 'None']));
    }

    #[TestDox('Verify that Text::translate() returns a JavaScript safe string')]
    public function testTranslateReturnsAJavascriptSafeKey()
    {
        $this->assertSame('foobar\\\'s', $this->object->translate('foobar\'s', [], true));
    }

    #[TestDox('Verify that Text::alt() returns the correct string for a key with no alt')]
    public function testAltReturnsTheCorrectStringForAKey()
    {
        $this->assertSame('Bar', $this->object->alt('FOO', ''));
    }

    #[TestDox('Verify that Text::alt() returns the correct string for a key with an alt')]
    public function testAltReturnsTheCorrectStringForAKeyWithAlt()
    {
        $this->assertSame('Car', $this->object->alt('FOO', 'GOO'));
    }

    #[TestDox('Verify that Text::alt() returns the correct string for a key with an alt and named parameters')]
    public function testAltReturnsTheCorrectStringForAKeyWithAltAndNamedParameters()
    {
        $this->assertSame('Green Car', $this->object->alt('FOO', 'BOO', ['%description%' => 'Green']));
    }

    #[TestDox('Verify that Text::plural() returns the input key when no plural key is found')]
    public function testPluralReturnsInputKeyWhenNoParamsPassed()
    {
        $this->assertSame('BAR', $this->object->plural('BAR', 0));
    }

    #[TestDox('Verify that Text::plural() returns the translated string when the pluralised key is found')]
    public function testPluralReturnsTranslatedStringWhenPluralisedKeyFound()
    {
        $this->assertSame('3 Bars', $this->object->plural('BAR', 3));
    }

    #[TestDox('Verify that Text::sprintf() returns the input key when no key is found')]
    public function testSprintfReturnsInputKeyWhenKeyNotFound()
    {
        $this->assertSame('BAR_NONE', $this->object->sprintf('BAR_NONE', 0));
    }

    #[TestDox('Verify that Text::sprintf() returns the translated string when the specified key is found')]
    public function testSprintfReturnsTranslatedStringWhenKeyFound()
    {
        $this->assertSame('I have 3 cars!', $this->object->sprintf('MANY_CARS', 3));
    }

    #[TestDox('Verify that Text::printf() returns the input key when no key is found')]
    public function testPrintfReturnsEmptyStringWhenKeyNotFound()
    {
        ob_start();
        $this->object->printf('BAR_NONE', 0);
        $return = ob_get_clean();

        $this->assertSame('BAR_NONE', $return);
    }

    #[TestDox('Verify that Text::printf() returns the translated string when the specified key is found')]
    public function testPrintfReturnsTranslatedStringWhenKeyFound()
    {
        ob_start();
        $this->object->printf('MANY_CARS', 3);
        $return = ob_get_clean();

        $this->assertSame('I have 3 cars!', $return);
    }
}
