<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Helper class for the Language package
 *
 * @since  2.0
 */
class LanguageHelper
{
    /**
     * Checks if a language exists.
     *
     * This is a simple, quick check for the directory that should contain language files for the given user.
     *
     * @param   string  $lang      Language to check.
     * @param   string  $basePath  Directory to check for the specified language.
     *
     * @return  boolean  True if the language exists.
     *
     * @since   2.0
     */
    public function exists(string $lang, string $basePath): bool
    {
        if (!$this->isValidLanguageTag($lang)) {
            return false;
        }

        return is_dir($this->getLanguagePath($basePath, $lang));
    }

    /**
     * Checks whether a string is a well-formed language tag.
     *
     * Accepts tags like `en`, `en-GB`, `sr-Latn-RS` or `zh-Hant`: a primary subtag of two or three lowercase letters,
     * followed by any number of subtags of two to eight letters or digits, each separated by a hyphen. Anything else,
     * in particular path separators and `..`, is rejected, so a valid tag can safely be used in a file name.
     *
     * @param   string  $lang  The language tag to check.
     *
     * @return  boolean
     *
     * @since   __DEPLOY_VERSION__
     */
    public function isValidLanguageTag(string $lang): bool
    {
        return preg_match('/^[a-z]{2,3}(?:-[A-Za-z0-9]{2,8})*$/D', $lang) === 1;
    }

    /**
     * Returns a associative array holding the metadata.
     *
     * @param   string  $lang  The name of the language.
     * @param   string  $path  The filepath to the language folder.
     *
     * @return  array|null  If $lang exists return key/value pair with the language metadata, otherwise return NULL.
     *
     * @since   2.0
     */
    public function getMetadata(string $lang, string $path): ?array
    {
        if (!$this->isValidLanguageTag($lang)) {
            return null;
        }

        $path = $this->getLanguagePath($path, $lang);
        $file = $lang . '.xml';

        $result = null;

        if (is_file("$path/$file")) {
            $result = $this->parseXMLLanguageFile("$path/$file");
        }

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    /**
     * Returns a list of known languages for an area
     *
     * @param   string  $basePath  The basepath to use
     *
     * @return  array  key/value pair with the language file and real name.
     *
     * @since   2.0
     */
    public function getKnownLanguages(string $basePath): array
    {
        return $this->parseLanguageFiles($this->getLanguagePath($basePath));
    }

    /**
     * Get the path to a language
     *
     * @param   string  $basePath  The basepath to use.
     * @param   string  $language  The language tag.
     *
     * @return  string  Path to the language folder
     *
     * @since   2.0
     * @throws  \InvalidArgumentException if the language tag is not a valid language tag
     */
    public function getLanguagePath(string $basePath, string $language = ''): string
    {
        $dir = $basePath . '/language';

        if (!empty($language)) {
            if (!$this->isValidLanguageTag($language)) {
                throw new \InvalidArgumentException(sprintf('Invalid language tag "%s"', $language));
            }

            $dir .= '/' . $language;
        }

        return $dir;
    }

    /**
     * Searches for language directories within a certain base dir.
     *
     * @param   string  $dir  directory of files.
     *
     * @return  array  Array holding the found languages as filename => real name pairs.
     *
     * @since   2.0
     */
    public function parseLanguageFiles(string $dir = ''): array
    {
        $languages = [];

        // Search main language directory for subdirectories
        foreach (glob($dir . '/*', GLOB_NOSORT | GLOB_ONLYDIR) as $directory) {
            // But only directories with lang code format
            if (preg_match('#/[a-z]{2,3}-[A-Z]{2}$#', $directory)) {
                $dirPathParts = pathinfo($directory);
                $file         = $directory . '/' . $dirPathParts['filename'] . '.xml';

                if (!is_file($file)) {
                    continue;
                }

                try {
                    // Get installed language metadata from xml file and merge it with lang array
                    if ($metadata = $this->parseXMLLanguageFile($file)) {
                        $languages = array_replace($languages, [$dirPathParts['filename'] => $metadata]);
                    }
                } catch (\RuntimeException $e) {
                }
            }
        }

        return $languages;
    }

    /**
     * Parse XML file for language information.
     *
     * @param   string  $path  Path to the XML files.
     *
     * @return  array|null  Array holding the found metadata as a key => value pair.
     *
     * @since   2.0
     * @throws  \RuntimeException
     */
    public function parseXmlLanguageFile(string $path): ?array
    {
        if (!is_readable($path)) {
            throw new \RuntimeException('File not found or not readable');
        }

        // Try to load the file
        $xml = simplexml_load_file($path);

        if (!$xml) {
            return null;
        }

        // Check that it's a metadata file
        if ((string) $xml->getName() != 'metafile') {
            return null;
        }

        $metadata = [];

        /** @var \SimpleXMLElement $child */
        foreach ($xml->metadata->children() as $child) {
            $metadata[$child->getName()] = (string) $child;
        }

        return $metadata;
    }
}
