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

/** Mutable MultiPolygon membership.
 * @extends MutableCollectionInterface<MutablePolygonInterface, array<array<array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}|\LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface>|\LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface>>
 */
interface MutableMultiPolygonInterface extends MultiPolygonInterface, MutableCollectionInterface
{
    /** @return list<MutablePolygonInterface> */
    public function getElements(): array;

    /** @return list<MutablePolygonInterface> */
    public function getPolygons(): array;
}
