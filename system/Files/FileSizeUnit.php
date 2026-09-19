<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Files;

use CodeIgniter\Exceptions\InvalidArgumentException;

/**
 * PHP 8.0-compatible stand-in for the PHP 8.1 backed enum.
 */
final class FileSizeUnit
{
    public const B  = 0;
    public const KB = 1;
    public const MB = 2;
    public const GB = 3;
    public const TB = 4;

    /**
     * Allows the creation of a FileSizeUnit from Strings like "kb" or "mb"
     *
     * @throws InvalidArgumentException
     */
    public static function fromString(string $unit): int
    {
        return match (strtolower($unit)) {
            'b'     => self::B,
            'kb'    => self::KB,
            'mb'    => self::MB,
            'gb'    => self::GB,
            'tb'    => self::TB,
            default => throw new InvalidArgumentException("Invalid unit: {$unit}"),
        };
    }
}
