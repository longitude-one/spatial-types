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

/**
 * LineString interface.
 *
 * The LineString type is a subtype of ST_Curve. The LineString type is instantiable.
 * A LineString instance has linear interpolation between Point values.
 * Each consecutive pair of Point values defines a line segment.
 * A line is a LineString value with exactly two points.
 * Functional linear-ring validation is an explicit consumer operation.
 *
 * The common curve category is represented by CurveInterface.
 */
interface LineStringInterface extends CurveInterface
{
    /**
     * Return the points that compose the line string.
     *
     * @return PointInterface[]
     */
    public function getElements(): array;

    /**
     * Return a point from the line string.
     *
     * @param int $index index of the point; immutable variants wrap negative indices, mutable variants require an existing zero-based index
     */
    public function getPoint(int $index): PointInterface;

    /**
     * Return the points that compose the line string.
     *
     * @return PointInterface[]
     */
    public function getPoints(): array;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int|null)[][]
     */
    public function toArray(): array;
}
