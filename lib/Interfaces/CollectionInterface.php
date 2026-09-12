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

    /**
     * Is this spatial collection empty?
     */
    public function isEmpty(): bool;
}
