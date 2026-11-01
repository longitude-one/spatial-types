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
