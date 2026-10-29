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

/** Mutable MultiPoint membership.
 * @extends MutableCollectionInterface<MutablePointInterface, array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}>
 */
interface MutableMultiPointInterface extends MultiPointInterface, MutableCollectionInterface
{
    /** @return list<MutablePointInterface> */
    public function getElements(): array;

    /** @return list<MutablePointInterface> */
    public function getPoints(): array;
}
