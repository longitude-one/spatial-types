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

namespace LongitudeOne\SpatialTypes\Interfaces;

use LongitudeOne\SpatialTypes\Value\Coordinates;

/** Immutable point read and copy contract. */
interface ImmutablePointInterface extends ImmutableSpatialInterface, PointInterface
{
    /**
     * Return an array of all coordinates.
     *
     * @return (float|int|null)[]
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
