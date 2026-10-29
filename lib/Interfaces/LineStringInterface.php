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
     * Is the line string empty?
     *
     * A line string is empty when it does not contain any point.
     */
    public function isEmpty(): bool;

    /**
     * Is the line string a line?
     *
     * A line is a LineString value with exactly two points.
     */
    public function isLine(): bool;

    /**
     * Return an array of coordinates.
     *
     * @return (null|float|int)[][]
     */
    public function toArray(): array;
}
