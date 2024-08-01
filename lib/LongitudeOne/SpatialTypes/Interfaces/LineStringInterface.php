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

namespace LongitudeOne\SpatialTypes\Interfaces;

/**
 * LineString interface.
 *
 * The LineString type is a subtype of ST_Curve. The LineString type is instantiable.
 * A LineString instance has linear interpolation between Point values.
 * Each consecutive pair of Point values defines a line segment.
 * A line is a LineString value with exactly two points.
 * A linear ring is a LineString value that is both closed and simple.
 *
 * As ST_Curve is not instantiable, this library does not implement it.
 */
interface LineStringInterface extends SpatialInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return LineStringInterface[]
     */
    public function getElements(): array;

    /**
     * Get a point of the linestring.
     *
     * @param int $index index of the point. -1 is the last point. -2 is the penultimate point, etc.
     */
    public function getPoint(int $index): PointInterface;

    /**
     * Return points composing the line string.
     *
     * @return PointInterface[]
     */
    public function getPoints(): array;

    /**
     * Is the line string a line?
     *
     * A line is a LineString value with exactly two points.
     */
    public function isLine(): bool;

    /**
     * A linear ring is a LineString value that is both closed and simple.
     */
    public function isRing(): bool;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int)[][]
     */
    public function toArray(): array;
}
