<?php

/**
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Language\Localise\AbstractLocalise;

/**
 * xx-XX localise class
 *
 * @since  1.0
 */
class Xx_XXLocalise extends AbstractLocalise
{
    /**
     * Returns the potential suffixes for a specific number of items
     *
     * @param   integer  $count  The number of items.
     *
     * @return  array  An array of potential suffixes.
     *
     * @since   1.0
     */
    public function getPluralSuffixes($count)
    {
        if ($count == 0) {
            $return = ['0'];
        } elseif ($count == 1) {
            $return = ['1'];
        } else {
            $return = ['MORE'];
        }

        return $return;
    }

    /**
     * Custom translitrate function to use.
     *
     * @param   string  $string  String to transliterate
     *
     * @return  integer  The number of chars to display when searching.
     *
     * @since   1.0
     */
    public function transliterate($string)
    {
        return str_replace(
            ['a', 'c', 'e', 'g'],
            ['b', 'd', 'f', 'h'],
            $string
        );
    }
}
