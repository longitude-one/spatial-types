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
 * This exception is thrown when mixing objects with non-compatible families.
 *
 * Example: When a developer tries to add a geometric point to a geographic linestring,
 * this exception is thrown.
 */
class InvalidFamilyException extends \Exception implements SpatialTypeExceptionInterface
{
}
