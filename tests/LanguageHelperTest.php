<?php

/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\LanguageHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Language\LanguageHelper.
 */
#[CoversClass(LanguageHelper::class)]
class LanguageHelperTest extends TestCase
{
    /**
     * Test language object
     *
     * @var  LanguageHelper
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
        $this->object   = new LanguageHelper();
    }

    #[TestDox('Verify that LanguageHelper::exists() locates the language directory')]
    public function testVerifyExistsLocatesTheLanguageDirectory()
    {
        $this->assertTrue($this->object->exists('en-GB', $this->testPath));
    }

    #[TestDox('Verify that LanguageHelper::getMetadata() returns the language metadata')]
    public function testVerifyGetMetadataReturnsTheLanguageMetadata()
    {
        $this->assertIsArray($this->object->getMetadata('en-GB', $this->testPath));
    }

    #[TestDox('Verify that LanguageHelper::getMetadata() returns null if metadata does not exist')]
    public function testVerifyGetMetadataReturnsNullIfMetadataDoesNotExist()
    {
        $this->assertNull($this->object->getMetadata('es-ES', $this->testPath));
    }

    #[TestDox('Verify that Language::getKnownLanguages() returns an array of known languages')]
    public function testVerifyGetKnownLanguagesReturnsAnArrayOfKnownLanguages()
    {
        $this->assertIsArray($this->object->getKnownLanguages($this->testPath));
    }

    #[TestDox('Verify that Language::getLanguagePath() returns the correct language path')]
    public function testVerifyGetLanguagePathReturnsTheCorrectLanguagePath()
    {
        $this->assertSame($this->testPath . '/language', $this->object->getLanguagePath($this->testPath));
    }

    #[TestDox('Verify that Language::parseLanguageFiles() returns an array')]
    public function testVerifyParseLanguageFilesReturnsAnArray()
    {
        $this->assertIsArray($this->object->parseLanguageFiles($this->testPath));
    }

    #[TestDox('Verify that Language::parseXMLLanguageFile() returns an array')]
    public function testVerifyParseXMLLanguageFileReturnsAnArray()
    {
        $this->assertIsArray($this->object->parseXMLLanguageFile($this->testPath . '/language/en-GB/en-GB.xml'));
    }

    #[TestDox('Verify that Language::parseXMLLanguageFile() returns null if the top XML tag is not metafile')]
    public function testVerifyParseXMLLanguageFileReturnsNullIfTheTopXMLTagIsNotMetafile()
    {
        $this->assertNull($this->object->parseXMLLanguageFile($this->testPath . '/language/xx-XX/xx-XX.xml'));
    }

    #[TestDox('Verify that Language::parseXMLLanguageFile() throws an exception if the file is not found')]
    public function testVerifyParseXMLLanguageFileThrowsAnExceptionIfTheFileIsNotFound()
    {
        $this->expectException(\RuntimeException::class);

        $this->object->parseXMLLanguageFile($this->testPath . '/xx-XX/xx-XX.xml');
    }
}
