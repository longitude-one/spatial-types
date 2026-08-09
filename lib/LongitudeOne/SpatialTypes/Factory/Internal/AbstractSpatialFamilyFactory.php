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

namespace LongitudeOne\SpatialTypes\Factory\Internal;

use LongitudeOne\SpatialTypes\Exception\MissingValueException;

/**
 * Creates geographic spatial types.
 *
 * @internal
 */
abstract class AbstractSpatialFamilyFactory implements SpatialFamilyFactoryInterface
{
    /**
     * Return a required coordinate.
     *
     * @template T of float|int
     *
     * @param null|T $coordinate coordinate
     * @param string $ordinal    coordinate ordinal
     *
     * @return T
     *
     * @throws MissingValueException when the coordinate is missing
     */
    protected static function requiredCoordinate(float|int|null $coordinate, string $ordinal): float|int
    {
        if (null === $coordinate) {
            throw new MissingValueException(sprintf('The %s coordinate is missing.', $ordinal));
        }

        return $coordinate;
    }
}
