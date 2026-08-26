<?php

/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Language;
use Joomla\Language\LanguageFactory;
use Joomla\Language\LanguageHelper;
use Joomla\Language\Localise\En_GBLocalise;
use Joomla\Language\MessageCatalogue;
use Joomla\Language\Parser\IniParser;
use Joomla\Language\ParserRegistry;
use Joomla\Language\Stemmer\Porteren;
use Joomla\Language\Text;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Language\Language.
 */
#[CoversClass(LanguageFactory::class)]
#[UsesClass(IniParser::class)]
#[UsesClass(Language::class)]
#[UsesClass(LanguageHelper::class)]
#[UsesClass(MessageCatalogue::class)]
#[UsesClass(ParserRegistry::class)]
#[UsesClass(Text::class)]
class LanguageFactoryTest extends TestCase
{
    /**
     * Test language object
     *
     * @var  LanguageFactory
     */
    protected $object;

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

        $this->testPath = __DIR__ . '/data';
        $this->object   = new LanguageFactory();
    }

    #[TestDox('Verify the default return of getDefaultLanguage()')]
    public function testTheDefaultReturnOfGetDefaultLanguage()
    {
        $this->assertSame('en-GB', $this->object->getDefaultLanguage());
    }

    #[TestDox('Verify the default return of getLanguageDirectory()')]
    public function testTheDefaultReturnOfGetLanguageDirectory()
    {
        $this->assertEmpty($this->object->getLanguageDirectory());
    }

    #[TestDox('Verify that getLocalise() returns the default localise class when none exists')]
    public function testVerifyGetLocaliseReturnsDefaultLocaliseWhenNoneExists()
    {
        $this->assertInstanceOf(En_GBLocalise::class, $this->object->getLocalise('fr-FR', $this->testPath));
    }

    #[TestDox('Verify that getLocalise() returns the correct localise class when it exists')]
    public function testVerifyGetLocaliseReturnedWhenExists()
    {
        // Class exists check in PHPUnit happens before we import the file in our method
        require_once $this->testPath . '/language/xx-XX/xx-XX.localise.php';

        $this->assertInstanceOf('\\Xx_XXLocalise', $this->object->getLocalise('xx-XX', $this->testPath));
    }

    #[TestDox('Verify that getLocalise() validates the cache when a localise object exists')]
    public function testVerifyGetLocaliseValidatesTheCacheWhenALocaliseObjectExists()
    {
        // Class exists check in PHPUnit happens before we import the file in our method
        require_once $this->testPath . '/language/xx-XX/xx-XX.localise.php';

        // Call the method once to fill the cache
        $this->object->getLocalise('xx-XX', $this->testPath);

        $this->assertInstanceOf('\\Xx_XXLocalise', $this->object->getLocalise('xx-XX', $this->testPath));
    }

    #[TestDox('Verify that getLanguage() returns a Language object')]
    public function testVerifyGetLanguageReturnsALanguageObject()
    {
        $this->assertInstanceOf(Language::class, $this->object->getLanguage(null, $this->testPath));
    }

    #[TestDox('Verify that getLanguage() throws an \InvalidArgumentException when no path is given')]
    public function testVerifyGetLanguageThrowsAnExceptionWhenNoPathIsGiven()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->object->getLanguage('es-ES');
    }

    #[TestDox('Verify that getText() returns a Text object')]
    public function testVerifyThatGetTextReturnsATextObject()
    {
        $language = $this->object->getLanguage(null, $this->testPath);
        $this->assertInstanceOf(Text::class, $this->object->getText($language));
    }

    #[TestDox('Verify getInstance() returns an instance of the correct object')]
    public function testGetStemmerReturnsAnInstanceOfTheCorrectObject()
    {
        $this->assertInstanceOf(Porteren::class, $this->object->getStemmer('porteren'));
    }

    #[TestDox('Verify getInstance() returns an instance of the correct object')]
    public function testGetStemmerThrowsAnExceptionIfTheObjectDoesNotExist()
    {
        $this->expectException(\RuntimeException::class);

        $this->object->getStemmer('unexisting');
    }

    #[TestDox('Verify setDefaultLanguage() returns the current object')]
    public function testSetDefaultLanguageReturnsTheCurrentObject()
    {
        $this->assertSame($this->object, $this->object->setDefaultLanguage('en-US'));
    }

    #[TestDox('Verify setLanguageDirectory() returns the current object')]
    public function testSetLanguageDirectoryReturnsTheCurrentObject()
    {
        $this->assertSame($this->object, $this->object->setLanguageDirectory($this->testPath));
    }

    #[TestDox('Verify setLanguageDirectory() throws an exception when a path does not exist')]
    public function testSetLanguageDirectoryThrowsAnExceptionWhenAPathDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->object->setLanguageDirectory(__DIR__ . '/negative-tester');
    }
}
