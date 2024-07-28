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
 * Range Exception class.
 *
 * This exception is thrown when a geodesic coordinate is out of range.
 *
 * @internal the library uses this exception internally and is always caught to throw a more explicit InvalidValueException
 */
final class RangeException extends \Exception implements SpatialTypeExceptionInterface
{
}
