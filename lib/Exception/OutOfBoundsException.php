<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024-2026
 * Copyright Longitude One 2024-2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Exception;

/**
 * This exception is thrown when a developer tries to get an instance point of an empty collection.
 *
 * For example, when you try to get the first point of an empty linestring, this exception is thrown.
 */
class OutOfBoundsException extends \OutOfBoundsException implements SpatialTypeExceptionInterface
{
}
