<?php

/**
 * @copyright  Copyright (C) 2005 - 2026 Open Source Matters, Inc. All rights reserved.
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
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the sprintf() based methods of Joomla\Language\Text.
 */
#[CoversClass(Text::class)]
#[UsesClass(Language::class)]
#[UsesClass(LanguageFactory::class)]
#[UsesClass(LanguageHelper::class)]
#[UsesClass(MessageCatalogue::class)]
#[UsesClass(ParserRegistry::class)]
#[UsesClass(IniParser::class)]
class TextFormatTest extends TestCase
{
    /**
     * Object being tested
     *
     * @var  Text
     */
    private Text $text;

    protected function setUp(): void
    {
        parent::setUp();

        $registry = new ParserRegistry();
        $registry->add(new IniParser());

        $language = new Language($registry, __DIR__ . '/data', 'en-GB');
        $language->load('format');

        $this->text = new Text($language);
    }

    #[TestDox('Text::sprintf() does not pass the options array to sprintf()')]
    public function testSprintfDoesNotFormatTheOptionsArray(): void
    {
        $this->assertSame('a and b', $this->text->sprintf('TWO_PLACEHOLDERS', 'a', 'b', ['jsSafe' => true]));
        $this->assertSame(
            'It' . \chr(92) . '\'s me',
            $this->text->sprintf('QUOTED', 'me', ['jsSafe' => true])
        );
    }

    #[TestDox('Text::sprintf() returns the key when the options array would be needed as an argument')]
    public function testSprintfWithOptionsAndTooFewArguments(): void
    {
        // Before, the options array filled the second placeholder: "a and Array" plus a warning
        $this->assertSame('TWO_PLACEHOLDERS', $this->text->sprintf('TWO_PLACEHOLDERS', 'a', ['jsSafe' => false]));
    }

    #[TestDox('Text::sprintf() returns the key when the translation has more placeholders than arguments')]
    public function testSprintfWithTooFewArguments(): void
    {
        $this->assertSame('TWO_PLACEHOLDERS', $this->text->sprintf('TWO_PLACEHOLDERS', 'a'));
    }

    #[TestDox('Text::sprintf() returns the key when the translation is not a valid format string')]
    public function testSprintfWithInvalidFormat(): void
    {
        $this->assertSame('PERCENT', $this->text->sprintf('PERCENT'));
    }

    #[TestDox('Text::printf() does not pass the options array to printf()')]
    public function testPrintfDoesNotFormatTheOptionsArray(): void
    {
        $this->expectOutputString('a and b');

        $this->assertSame(7, $this->text->printf('TWO_PLACEHOLDERS', 'a', 'b', ['jsSafe' => true]));
    }

    #[TestDox('Text::printf() prints the key when the translation has more placeholders than arguments')]
    public function testPrintfWithTooFewArguments(): void
    {
        $this->expectOutputString('TWO_PLACEHOLDERS');

        $this->assertSame(16, $this->text->printf('TWO_PLACEHOLDERS', 'a', ['jsSafe' => true]));
    }

    #[TestDox('Text::plural() does not pass the options array to sprintf()')]
    public function testPluralDoesNotFormatTheOptionsArray(): void
    {
        $this->assertSame('2 items in the cart', $this->text->plural('ITEMS', 2, 'the cart', ['jsSafe' => true]));
    }

    #[TestDox('Text::plural() returns the key when the translation has more placeholders than arguments')]
    public function testPluralWithTooFewArguments(): void
    {
        $this->assertSame('ITEMS', $this->text->plural('ITEMS', 2, ['jsSafe' => true]));
    }
}
