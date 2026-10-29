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

/** Immutable collection read and copy contract. */
interface ImmutableCollectionInterface extends CollectionInterface, ImmutableSpatialInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return ImmutableSpatialInterface[]
     */
    public function getElements(): array;
}
