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
 * Multi-polygon interface.
 *
 * The MultiPolygon type corresponds to the instantiable ST_MultiPolygon subtype
 * of ST_MultiSurface defined by ISO/IEC 13249-3. Its elements are restricted to
 * Polygon values.
 *
 * ST_MultiSurface is not instantiable, so this library does not expose a
 * corresponding interface.
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
}
