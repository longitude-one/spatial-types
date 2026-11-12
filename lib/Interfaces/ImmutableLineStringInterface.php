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

/** Immutable linestring read and copy contract. */
interface ImmutableLineStringInterface extends ImmutableCurveInterface, LineStringInterface
{
    /**
     * Return the points that compose the line string.
     *
     * @return ImmutablePointInterface[]
     */
    public function getElements(): array;

    /** Return the immutable end point, or null for EMPTY. */
    public function getEndPoint(): ?ImmutablePointInterface;

    /**
     * Return a point from the line string.
     *
     * @param int $index index of the point. -1 is the last point. -2 is the penultimate point, etc.
     */
    public function getPoint(int $index): ImmutablePointInterface;

    /**
     * Return the points that compose the line string.
     *
     * @return ImmutablePointInterface[]
     */
    public function getPoints(): array;

    /** Return the immutable start point, or null for EMPTY. */
    public function getStartPoint(): ?ImmutablePointInterface;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int|null)[][]
     */
    public function toArray(): array;

    /**
     * Return a new line string with replacement coordinates.
     *
     * The returned line string preserves this instance's family, dimension,
     * and Spatial Reference Identifier (SRID). Each tuple must therefore match
     * this instance's coordinate layout: XY, XYM, XYZ, or XYZM.
     *
     * @param array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}> $coordinates replacement coordinates
     */
    public function withArrayOfCoordinates(array $coordinates): static;

    /**
     * Return a new line string with one replacement point.
     *
     * The returned line string preserves this instance's family, dimension,
     * and Spatial Reference Identifier (SRID). The coordinate dimension must
     * match the point selected by the index.
     *
     * @param int         $pointIndex  index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates replacement point coordinates
     */
    public function withPoint(int $pointIndex, Coordinates $coordinates): static;
}
