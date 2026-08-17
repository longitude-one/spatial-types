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

/**
 * LineString interface.
 *
 * The LineString type is a subtype of ST_Curve. The LineString type is instantiable.
 * A LineString instance has linear interpolation between Point values.
 * Each consecutive pair of Point values defines a line segment.
 * A line is a LineString value with exactly two points.
 * Linear-ring validation is provided by Validator\Constraints\Ring.
 *
 * As ST_Curve is not instantiable, this library does not implement it.
 */
interface LineStringInterface extends SpatialInterface
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
     * @param int $index index of the point. -1 is the last point. -2 is the penultimate point, etc.
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
     * @return (float|int)[][]
     */
    public function toArray(): array;

    /**
     * Return a new line string with replacement coordinates.
     *
     * The returned line string preserves this instance's family, dimension,
     * and Spatial Reference Identifier (SRID). Each tuple must therefore match
     * this instance's coordinate layout: XY, XYM, XYZ, or XYZM.
     *
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates replacement coordinates
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
