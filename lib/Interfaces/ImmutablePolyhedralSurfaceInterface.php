<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
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
     * @return (float|int|null)[][][][]
     */
    public function toArray(): array;

    /**
     * Return an independent surface with replacement patch coordinates.
     *
     * @param array<array<array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}>>> $coordinates Patch coordinates
     */
    public function withArrayOfCoordinates(array $coordinates): static;

    /**
     * Return a new polyhedral surface with one replacement polygon.
     *
     * @param int                                                                                                               $patchIndex  index of the polygon to replace; negative indexes count from the end
     * @param array<array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}>> $coordinates replacement polygon coordinates
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
     * @param array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}> $coordinates replacement ring coordinates
     */
    public function withRing(int $patchIndex, int $ringIndex, array $coordinates): static;
}
