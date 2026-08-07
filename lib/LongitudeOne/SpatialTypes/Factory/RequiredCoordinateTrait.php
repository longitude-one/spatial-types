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

namespace LongitudeOne\SpatialTypes\Factory;

use LongitudeOne\SpatialTypes\Exception\MissingValueException;

/**
 * Ensures a coordinate required by a spatial dimension is present.
 *
 * @internal
 */
trait RequiredCoordinateTrait
{
    /**
     * Return a required coordinate.
     *
     * @template T of \DateTimeInterface|float|int
     *
     * @param null|T $coordinate coordinate
     * @param string $ordinal    coordinate ordinal
     *
     * @return T
     *
     * @throws MissingValueException when the coordinate is missing
     */
    private static function requiredCoordinate(\DateTimeInterface|float|int|null $coordinate, string $ordinal): \DateTimeInterface|float|int
    {
        if (null === $coordinate) {
            throw new MissingValueException(sprintf('The %s coordinate is missing.', $ordinal));
        }

        return $coordinate;
    }
}
