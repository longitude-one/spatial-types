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

use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** Immutable spatial read and copy contract. */
interface ImmutableSpatialInterface extends SpatialInterface
{
    /**
     * Return a deep copy preserving this value's concrete type, coordinates,
     * family, dimension, complete spatial reference and membership order.
     * Every spatial descendant is independently copied with its own context.
     * Immutable auxiliary values, including Coordinates and SpatialReference,
     * may be shared. The original spatial graph remains unchanged.
     */
    public function copy(): static;

    /**
     * Convert this spatial object to its array representation.
     *
     * The array contains only nested arrays of floats and integers, or is empty
     * when the spatial object corresponds to the empty set.
     *
     * Some information is lost in this representation. For example, a line string
     * containing two points has the same representation as a multi-point containing
     * those points, and the SRID is not exported.
     *
     * Use the longitude-one/spatial-writer library to export all data.
     *
     * @return (float|int|null)[]|(float|int|null)[][]|(float|int|null)[][][]|(float|int|null)[][][][]
     */
    public function toArray(): array;

    /**
     * Return a copy declared in another spatial reference without transforming
     * its coordinates. Use a coordinate transformation service for reprojection.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static;

    /**
     * Return a new spatial object with the given Spatial Reference Identifier (SRID).
     *
     * Coordinates, family, and dimension are preserved. For aggregate spatial
     * objects, every contained spatial object receives the requested SRID.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static;
}
