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
 * Multi-line string interface.
 *
 * The MultiLineString type corresponds to the instantiable ST_MultiLineString
 * subtype of ST_MultiCurve defined by ISO/IEC 13249-3. Its elements are
 * restricted to LineString values.
 */
interface MultiLineStringInterface extends CollectionInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return LineStringInterface[]
     */
    public function getElements(): array;

    /**
     * Return the line strings that compose the multi-line string.
     *
     * @return LineStringInterface[]
     */
    public function getLineStrings(): array;

    /**
     * Return an array of coordinates.
     *
     * @return (float|int)[][][]
     */
    public function toArray(): array;
}
