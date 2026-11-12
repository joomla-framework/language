<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\LanguageFactory;
use Joomla\Language\Stemmer\Porteren;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the deprecated stemmer API triggers deprecation notices.
 */
#[CoversClass(LanguageFactory::class)]
#[CoversClass(Porteren::class)]
class StemmerDeprecationTest extends TestCase
{
    /**
     * Deprecation messages raised during the test
     *
     * @var  string[]
     */
    private array $deprecations = [];

    protected function setUp(): void
    {
        parent::setUp();

        set_error_handler(
            function (int $errno, string $errstr): bool {
                $this->deprecations[] = $errstr;

                return true;
            },
            E_USER_DEPRECATED
        );
    }

    protected function tearDown(): void
    {
        restore_error_handler();

        parent::tearDown();
    }

    #[TestDox('Instantiating Porteren triggers a deprecation notice')]
    public function testPorterenIsDeprecated(): void
    {
        new Porteren();

        $this->assertCount(1, $this->deprecations);
        $this->assertStringContainsString(Porteren::class . ' is deprecated', $this->deprecations[0]);
    }

    #[TestDox('LanguageFactory::getStemmer() triggers a deprecation notice')]
    public function testGetStemmerIsDeprecated(): void
    {
        $this->assertInstanceOf(Porteren::class, (new LanguageFactory())->getStemmer('porteren'));

        $this->assertStringContainsString('getStemmer() is deprecated', $this->deprecations[0]);
    }
}
