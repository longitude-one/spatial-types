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

namespace LongitudeOne\SpatialTypes\Interfaces;

use LongitudeOne\SpatialTypes\Value\Coordinates;

/** Immutable point read and copy contract. */
interface ImmutablePointInterface extends ImmutableSpatialInterface, PointInterface
{
    /**
     * Return an array of all coordinates.
     *
     * @return (null|float|int)[]
     */
    public function toArray(): array;

    /**
     * Return a new point with the supplied normalized coordinates.
     *
     * The coordinates must use the same dimension as this point. The point's
     * family and Spatial Reference Identifier (SRID) are preserved.
     *
     * @param Coordinates $coordinates replacement coordinates
     */
    public function withCoordinates(Coordinates $coordinates): static;
}
