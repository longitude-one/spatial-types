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
 * Multi-polygon interface.
 *
 * The MultiPolygon type corresponds to the instantiable ST_MultiPolygon subtype
 * of ST_MultiSurface defined by ISO/IEC 13249-3. Its elements are restricted to
 * Polygon values.
 */
interface MultiPolygonInterface extends CollectionInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return PolygonInterface[]
     */
    public function getElements(): array;

    /**
     * Return the polygons that compose the multi-polygon.
     *
     * @return PolygonInterface[]
     */
    public function getPolygons(): array;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int)[][][][]
     */
    public function toArray(): array;

    /**
     * Return a new multi-polygon with one replacement ring.
     *
     * @param int                                                                                              $polygonIndex index of the polygon to replace; negative indexes count from the end
     * @param int                                                                                              $ringIndex    index of the ring to replace; negative indexes count from the end
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates  replacement ring coordinates
     */
    public function withLineString(int $polygonIndex, int $ringIndex, array $coordinates): static;

    /**
     * Return a new multi-polygon with one replacement point.
     *
     * @param int         $polygonIndex index of the polygon to replace; negative indexes count from the end
     * @param int         $ringIndex    index of the ring to replace; negative indexes count from the end
     * @param int         $pointIndex   index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates  replacement point coordinates
     */
    public function withPoint(int $polygonIndex, int $ringIndex, int $pointIndex, Coordinates $coordinates): static;

    /**
     * Return a new multi-polygon with one replacement polygon.
     *
     * @param int                                                                                                     $polygonIndex index of the polygon to replace; negative indexes count from the end
     * @param array<array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>> $coordinates  replacement polygon coordinates
     */
    public function withPolygon(int $polygonIndex, array $coordinates): static;
}
