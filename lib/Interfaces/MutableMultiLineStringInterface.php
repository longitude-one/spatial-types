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

/** Mutable MultiLineString membership.
 * @extends MutableCollectionInterface<MutableLineStringInterface, array<array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}|\LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface>>
 */
interface MutableMultiLineStringInterface extends MultiLineStringInterface, MutableCollectionInterface
{
    /** @return list<MutableLineStringInterface> */
    public function getElements(): array;

    /** @return list<MutableLineStringInterface> */
    public function getLineStrings(): array;
}
