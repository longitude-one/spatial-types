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

/** Immutable polyhedralsurface read and copy contract. */
interface ImmutablePolyhedralSurfaceInterface extends ImmutableSpatialInterface, PolyhedralSurfaceInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return ImmutablePolygonInterface[]
     */
    public function getElements(): array;

    /**
     * Return a patch using wrapped indexes, with negative indexes from the end.
     *
     * @param int $index Patch index
     */
    public function getPatch(int $index): ImmutablePolygonInterface;

    /**
     * Return the polygons that compose the polyhedral surface.
     *
     * @return ImmutablePolygonInterface[]
     */
    public function getPatches(): array;

    /**
     * Return an array of coordinates.
     *
     * @return (null|float|int)[][][][]
     */
    public function toArray(): array;

    /**
     * Return an independent surface with replacement patch coordinates.
     *
     * @param array<array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>>> $coordinates Patch coordinates
     */
    public function withArrayOfCoordinates(array $coordinates): static;

    /**
     * Return a new polyhedral surface with one replacement polygon.
     *
     * @param int                                                                                                               $patchIndex  index of the polygon to replace; negative indexes count from the end
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}>> $coordinates replacement polygon coordinates
     */
    public function withPatch(int $patchIndex, array $coordinates): static;

    /**
     * Return a new polyhedral surface with one replacement point.
     *
     * @param int         $patchIndex  index of the polygon to replace; negative indexes count from the end
     * @param int         $ringIndex   index of the ring to replace; negative indexes count from the end
     * @param int         $pointIndex  index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates replacement point coordinates
     */
    public function withPoint(int $patchIndex, int $ringIndex, int $pointIndex, Coordinates $coordinates): static;

    /**
     * Return a new polyhedral surface with one replacement ring.
     *
     * @param int                                                                                                        $patchIndex  index of the polygon to replace; negative indexes count from the end
     * @param int                                                                                                        $ringIndex   index of the ring to replace; negative indexes count from the end
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates replacement ring coordinates
     */
    public function withRing(int $patchIndex, int $ringIndex, array $coordinates): static;
}
