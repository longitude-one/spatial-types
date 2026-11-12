<?php
/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
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
