<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests\Parser;

use Joomla\Language\Language;
use Joomla\Language\Parser\IniParser;
use Joomla\Language\ParserRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Language\Parser\IniParser.
 */
#[CoversClass(IniParser::class)]
#[UsesClass(Language::class)]
#[UsesClass(ParserRegistry::class)]
class IniParserTest extends TestCase
{
    /**
     * Path to the test data
     *
     * @var  string
     */
    private string $testPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testPath = \dirname(__DIR__) . '/data';

        putenv('JOOMLA_LANGUAGE_TEST_SECRET=s3cr3t');
    }

    protected function tearDown(): void
    {
        putenv('JOOMLA_LANGUAGE_TEST_SECRET');

        parent::tearDown();
    }

    #[TestDox('IniParser::loadFile() does not expand ${...} variables in values')]
    public function testLoadFileDoesNotExpandVariables(): void
    {
        $strings = (new IniParser())->loadFile($this->testPath . '/language/en-GB/en-GB.variables.ini');

        $this->assertSame('${JOOMLA_LANGUAGE_TEST_SECRET}', $strings['SECRET']);
        $this->assertSame('Hello ${UNDEFINED_VARIABLE} world', $strings['INLINE']);
    }

    #[TestDox('IniParser::loadFile() unescapes quotes and backslashes in quoted values')]
    public function testLoadFileUnescapesQuotesAndBackslashes(): void
    {
        $backslash = \chr(92);
        $strings   = (new IniParser())->loadFile($this->testPath . '/language/en-GB/en-GB.variables.ini');

        $this->assertSame('He said "hi"', $strings['ESCAPED_QUOTES']);
        $this->assertSame('C:' . $backslash . 'path', $strings['BACKSLASH']);
        $this->assertSame('Line' . $backslash . 'nbreak', $strings['NEWLINE']);
    }

    #[TestDox('Language does not expose environment variables through translations')]
    public function testLanguageDoesNotExposeEnvironmentVariables(): void
    {
        $registry = new ParserRegistry();
        $registry->add(new IniParser());

        $language = new Language($registry, $this->testPath, 'en-GB');
        $language->load('variables');

        $this->assertSame('${JOOMLA_LANGUAGE_TEST_SECRET}', $language->translate('SECRET'));
        $this->assertSame("Line\nbreak", $language->translate('NEWLINE'));
    }

    #[TestDox('IniParser::loadFile() reports the file name and the parser error in the right order')]
    public function testLoadFileErrorMessage(): void
    {
        $filename = $this->testPath . '/bad.ini';

        try {
            (new IniParser())->loadFile($filename);

            $this->fail('Expected a RuntimeException');
        } catch (\RuntimeException $exception) {
            $this->assertStringStartsWith('Could not process file `' . $filename . '`: ', $exception->getMessage());
            $this->assertStringContainsString('syntax error', $exception->getMessage());
        }
    }

    #[TestDox('IniParser::loadFile() reports the error of a missing file')]
    public function testLoadFileMissingFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Could not process file `.*missing\.ini`: .*Failed to open stream/');

        (new IniParser())->loadFile($this->testPath . '/missing.ini');
    }

    #[TestDox('Language records files that cannot be parsed in the error files')]
    public function testLanguageRecordsParseErrors(): void
    {
        $registry = new ParserRegistry();
        $registry->add(new IniParser());

        $language = new Language($registry, $this->testPath, 'en-GB');

        $this->assertFalse($language->load('bad'));

        $errorFiles = $language->getErrorFiles();
        $filename   = $this->testPath . '/language/en-GB/en-GB.bad.ini';

        $this->assertArrayHasKey($filename, $errorFiles);
        $this->assertStringContainsString('syntax error', $errorFiles[$filename]);
    }
}
