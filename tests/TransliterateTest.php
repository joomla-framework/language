<?php

/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Tests;

use Joomla\Language\Transliterate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Test class for Transliterate.
 */
#[CoversClass(Transliterate::class)]
class TransliterateTest extends TestCase
{
    /**
     * @var  Transliterate
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new Transliterate();
    }

    /**
     * Data provider for testUtf8_latin_to_ascii()
     *
     * @return  array
     */
    public static function dataProvider()
    {
        return [
            ['Weiß', 'Weiss', 0],
            ['Goldmann', 'Goldmann', 0],
            ['Göbel', 'Goebel', 0],
            ['Weiss', 'Weiss', 0],
            ['Göthe', 'Goethe', 0],
            ['Götz', 'Goetz', 0],
            ['Weßling', 'Wessling', 0],
            ['Šíleně', 'Silene', 0],
            ['žluťoučký', 'zlutoucky', 0],
            ['Vašek', 'Vasek', 0],
            ['úpěl', 'upel', 0],
            ['olol', 'olol', 0],
            ['Göbel', 'Goebel', -1],
            ['Göbel', 'Göbel', 1],
        ];
    }

    /**
     * @param   string   $word    Word to transliterate
     * @param   string   $result  Expected test result
     * @param   integer  $case    Optionally specify upper or lower case. Default to 0 (both).
     */
    #[DataProvider('dataProvider')]
    #[TestDox('Verify a UTF-8 string is transliterated correctly')]
    public function testVerifyAUTF8StringIsTransliteratedCorrectly($word, $result, $case)
    {
        $this->assertSame($result, $this->object->utf8_latin_to_ascii($word, $case));
    }
}
