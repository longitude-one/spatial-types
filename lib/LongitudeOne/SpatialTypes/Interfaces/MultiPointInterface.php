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
 * The MultiPoint type is a subtype of GeometryCollection. The MultiPoint type is instantiable. A
 * MultiPoint value is a 0-dimensional geometry. The elements of aMultiPoint value are restricted
 * to Point values. The ST_Point values are not connected or ordered. A MultiPoint value is
 * simple if and only if no two Point values in the ST_MultiPoint value are equal.
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
     * Return points composing the line string.
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
}
