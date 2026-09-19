<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! class_exists('Locale', false)) {
    /**
     * Minimal intl fallback when ext-intl is not enabled on the host.
     */
    class Locale
    {
        private static string $default = 'en';

        public static function getDefault(): string
        {
            return self::$default;
        }

        public static function setDefault(string $locale): bool
        {
            self::$default = $locale !== '' ? $locale : 'en';

            return true;
        }
    }
}

if (! class_exists('IntlDateFormatter', false)) {
    /**
     * Minimal intl fallback used by CodeIgniter Time formatting.
     */
    class IntlDateFormatter
    {
        public const NONE   = -1;
        public const SHORT  = 1;
        public const MEDIUM = 2;
        public const LONG   = 3;
        public const FULL   = 4;

        public static function formatObject($datetime, $format = null, $locale = null): string
        {
            if (! $datetime instanceof DateTimeInterface) {
                return '';
            }

            if (is_string($format) && $format !== '') {
                $phpFormat = strtr($format, [
                    'yyyy' => 'Y',
                    'MM'   => 'm',
                    'dd'   => 'd',
                    'HH'   => 'H',
                    'mm'   => 'i',
                    'ss'   => 's',
                ]);

                return $datetime->format($phpFormat);
            }

            return $datetime->format('Y-m-d H:i:s');
        }
    }
}

if (! function_exists('array_is_list')) {
    /**
     * PHP 8.0 polyfill for array_is_list() (PHP 8.1+).
     *
     * @param array<int|string, mixed> $array
     */
    function array_is_list(array $array): bool
    {
        $expectedKey = 0;

        foreach ($array as $key => $_) {
            if ($key !== $expectedKey) {
                return false;
            }

            $expectedKey++;
        }

        return true;
    }
}
