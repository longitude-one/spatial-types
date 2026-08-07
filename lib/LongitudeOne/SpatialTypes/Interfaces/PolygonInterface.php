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
}
