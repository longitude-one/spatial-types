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

/** A connected surface of polygon patches, with an optional open boundary. */
interface PolyhedralSurfaceInterface extends SpatialInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return PolygonInterface[]
     */
    public function getElements(): array;

    /**
     * Return a patch by index; indexing conventions are defined by the variant.
     *
     * @param int $index Patch index
     */
    public function getPatch(int $index): PolygonInterface;

    /**
     * Return the polygons that compose the polyhedral surface.
     *
     * @return PolygonInterface[]
     */
    public function getPatches(): array;

    /**
     * Return an array of coordinates.
     *
     * @return (null|float|int)[][][][]
     */
    public function toArray(): array;
}
