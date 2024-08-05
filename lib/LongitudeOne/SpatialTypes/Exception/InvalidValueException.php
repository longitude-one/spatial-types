<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Exception;

/**
 * This exception is thrown when a coordinate is invalid.
 */
class InvalidValueException extends \InvalidArgumentException implements SpatialTypeExceptionInterface
{
    public const OUT_OF_RANGE_LATITUDE = 'Out of range latitude value, latitude must be between -90 and 90, got "%s".';
    public const OUT_OF_RANGE_LONGITUDE = 'Out of range longitude value, longitude must be between -180 and 180, got "%s".';
    public const OUT_OF_RANGE_MINUTE = 'Out of range minute value, minute must be between 0 and 59, got "%s".';
    public const OUT_OF_RANGE_SECOND = 'Out of range second value, second must be between 0 and 59, got "%s".';
}
