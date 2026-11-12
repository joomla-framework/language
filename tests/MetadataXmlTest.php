<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\LanguageHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests LanguageHelper::parseXmlLanguageFile() with malformed metadata files.
 */
#[CoversClass(LanguageHelper::class)]
class MetadataXmlTest extends TestCase
{
    /**
     * Warnings raised during the test
     *
     * @var  string[]
     */
    private array $warnings = [];

    protected function setUp(): void
    {
        parent::setUp();

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

    #[TestDox('LanguageHelper::parseXmlLanguageFile() returns null without warnings for malformed XML')]
    public function testMalformedXml(): void
    {
        $this->assertNull((new LanguageHelper())->parseXmlLanguageFile(__DIR__ . '/data/metadata/broken.xml'));
        $this->assertSame([], $this->warnings);
        $this->assertFalse(libxml_use_internal_errors(), 'The libxml error handling setting must be restored');
        $this->assertFalse(libxml_get_last_error(), 'The libxml errors must be cleared');
    }

    #[TestDox('LanguageHelper::parseXmlLanguageFile() returns null without warnings when the metadata element is missing')]
    public function testMissingMetadataElement(): void
    {
        $this->assertNull((new LanguageHelper())->parseXmlLanguageFile(__DIR__ . '/data/metadata/no-metadata.xml'));
        $this->assertSame([], $this->warnings);
    }

    #[TestDox('LanguageHelper::parseXmlLanguageFile() still reads a valid metadata file')]
    public function testValidFile(): void
    {
        $metadata = (new LanguageHelper())->parseXmlLanguageFile(__DIR__ . '/data/language/en-GB/en-GB.xml');

        $this->assertSame('en-GB', $metadata['tag']);
        $this->assertSame([], $this->warnings);
    }
}
