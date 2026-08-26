<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests\Localise;

use Joomla\Language\Localise\AbstractLocalise;
use Joomla\Language\Tests\stubs\StubLocalise;
use Joomla\Language\Transliterate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Language\Localise\AbstractLocalise.
 */
#[CoversClass(AbstractLocalise::class)]
#[UsesClass(Transliterate::class)]
class AbstractLocaliseTest extends TestCase
{
    #[TestDox('Verify that the transliterate method calls the defined transliterator')]
    public function testTransliterateCallsDefinedTransliterator()
    {
        $localise = new StubLocalise();

        $this->assertSame('asi', $localise->transliterate('Así'));
    }

    #[TestDox('Verify that the plural suffixes are returned')]
    public function testGetPluralSuffixesCallsTheDefinedMethod()
    {
        $localise = new StubLocalise();

        $this->assertSame(['1'], $localise->getPluralSuffixes(1));
    }
}
