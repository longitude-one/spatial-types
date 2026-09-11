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
 * Polygon interface.
 *
 * The Polygon type corresponds to the instantiable ST_Polygon subtype of
 * ST_CurvePolygon defined by ISO/IEC 13249-3. Its boundary is composed of
 * linear rings.
 */
interface PolygonInterface extends SpatialInterface
{
    /**
     * Return a ring from the polygon.
     *
     * @param int $index Index of the ring
     */
    public function getRing(int $index): LineStringInterface;

    /**
     * Return the rings that compose the polygon.
     *
     * @return LineStringInterface[]
     */
    public function getRings(): array;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int)[][][]
     */
    public function toArray(): array;

    /**
     * Return a new polygon with replacement ring coordinates.
     *
     * The returned polygon preserves this instance's family, dimension, and
     * Spatial Reference Identifier (SRID). Every coordinate tuple must match
     * this instance's coordinate layout and every ring must be closed.
     *
     * @param array<array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>> $coordinates replacement ring coordinates
     */
    public function withArrayOfCoordinates(array $coordinates): static;

    /**
     * Return a new polygon with one replacement point in a ring.
     *
     * The returned polygon preserves this instance's family, dimension, and
     * Spatial Reference Identifier (SRID). A replacement of the first or last
     * point of a ring is applied to both endpoints to preserve ring closure.
     *
     * @param int         $ringIndex   index of the ring to replace; negative indexes count from the end
     * @param int         $pointIndex  index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates replacement point coordinates
     */
    public function withPoint(int $ringIndex, int $pointIndex, Coordinates $coordinates): static;

    /**
     * Return a new polygon with one replacement ring.
     *
     * The returned polygon preserves this instance's family, dimension, and
     * Spatial Reference Identifier (SRID). The replacement coordinates must
     * describe a closed ring using this polygon's coordinate layout.
     *
     * @param int                                                                                              $ringIndex   index of the ring to replace; negative indexes count from the end
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates replacement ring coordinates
     */
    public function withRing(int $ringIndex, array $coordinates): static;
}
