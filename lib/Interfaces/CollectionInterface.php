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

/**
 * Spatial collection interface.
 *
 * Defines the common operations for spatial types composed of other spatial
 * objects. Concrete collection types may restrict the type, dimension, family,
 * or spatial reference system of their elements.
 */
interface CollectionInterface extends SpatialInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return SpatialInterface[]
     */
    public function getElements(): array;
}
