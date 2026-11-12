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

/** Mutable MultiPoint membership.
 * @extends MutableCollectionInterface<MutablePointInterface, array{0: float|int|string|null, 1: float|int|string|null, 2?: float|int|null, 3?: float|int|null}>
 */
interface MutableMultiPointInterface extends MultiPointInterface, MutableCollectionInterface
{
    /** @return list<MutablePointInterface> */
    public function getElements(): array;

    /** @return list<MutablePointInterface> */
    public function getPoints(): array;
}
