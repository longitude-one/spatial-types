<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
