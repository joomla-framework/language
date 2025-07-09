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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Joomla\Language\Localise\AbstractLocalise.
 */
class AbstractLocaliseTest extends TestCase
{
    /**
     * @testdox  Verify that the transliterate method calls the defined transliterator
     *
     * @covers   \Joomla\Language\Localise\AbstractLocalise
     * @uses     \Joomla\Language\Transliterate
     */
    public function testTransliterateCallsDefinedTransliterator()
    {
        $localise = new StubLocalise();

        $this->assertSame('asi', $localise->transliterate('Así'));
    }

    /**
     * @testdox  Verify that the plural suffixes are returned
     *
     * @covers   Joomla\Language\Localise\AbstractLocalise
     */
    public function testGetPluralSuffixesCallsTheDefinedMethod()
    {
        $localise = new StubLocalise();

        $this->assertSame(['1'], $localise->getPluralSuffixes(1));
    }
}
