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
