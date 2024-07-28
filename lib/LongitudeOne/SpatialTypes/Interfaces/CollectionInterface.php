<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Interfaces;

/**
 * Collection interface.
 *
 * Some spatial objects are a collection of other spatial objects.
 *
 * This interface defines the methods that must be implemented by a collection to retrieve its elements.
 */
interface CollectionInterface extends SpatialInterface
{
    /**
     * Return an ordered array of spatial interfaces in the collection.
     *
     * @return array<int, SpatialInterface>
     */
    public function getElements(): array;

    /**
     * Return an array of spatial interfaces in the collection.
     *
     * @return SpatialInterface[]
     */
    public function toArray(): array;
}
