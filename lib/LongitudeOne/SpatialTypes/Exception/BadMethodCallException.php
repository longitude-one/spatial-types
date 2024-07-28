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

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;

/**
 * This exception is thrown when a method should not be called in a class.
 *
 * As example, if a developer constructs a PointM object, the method "hasZ" should not be called.
 * If the method "hasZ" is still called, this exception is thrown.
 */
class BadMethodCallException extends \BadMethodCallException implements SpatialTypeExceptionInterface
{
    /**
     * Create a new instance of the exception when a method should not be called in a class.
     *
     * @param string        $method    the method name
     * @param DimensionEnum $dimension the dimension
     */
    public static function create(string $method, DimensionEnum $dimension): static
    {
        return new BadMethodCallException(sprintf('The method "%s" cannot be called with a spatial object with dimensions "%s".', $method, $dimension->value));
    }
}
