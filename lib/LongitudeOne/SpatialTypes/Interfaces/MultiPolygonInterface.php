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
 * MultiPolygon interface.
 *
 * MultiPolygon type is a subtype of MultiSurface. The MultiPolygon type is instantiable.
 * The elements of a MultiPolygon value are restricted to Polygon values.
 *
 * Nota: This library does not implement MultiSurface as this type is not instantiable, according to the ISO-13249-3 standard.
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
     * Return each polygon composing the multi-polygon.
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
