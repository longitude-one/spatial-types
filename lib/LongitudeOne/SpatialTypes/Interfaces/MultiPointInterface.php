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
 * Multi-point interface.
 *
 * The MultiPoint type corresponds to the instantiable ST_MultiPoint subtype of
 * ST_GeomCollection defined by ISO/IEC 13249-3. It is a zero-dimensional
 * geometry whose elements are Point values. The points are neither connected
 * nor ordered. A multi-point is simple if and only if no two of its points are
 * equal.
 */
interface MultiPointInterface extends CollectionInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return PointInterface[]
     */
    public function getElements(): array;

    /**
     * Return the points that compose the multi-point.
     *
     * @return PointInterface[]
     */
    public function getPoints(): array;

    /**
     * Is this multipoint simple?
     * A MultiPoint value is simple if and only if no two Point values in the MultiPoint value are equal.
     */
    public function isSimple(): bool;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int)[][]
     */
    public function toArray(): array;

    /**
     * Return a new multi-point with one replacement point.
     *
     * The returned multi-point preserves this instance's family, dimension,
     * and Spatial Reference Identifier (SRID). The coordinate dimension must
     * match the point selected by the index.
     *
     * @param int         $pointIndex  index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates replacement point coordinates
     */
    public function withPoint(int $pointIndex, Coordinates $coordinates): static;
}
