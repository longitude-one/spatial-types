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
