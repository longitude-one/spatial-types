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

namespace LongitudeOne\SpatialTypes\Internal;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;

/** @internal Methods of the one public SpatialBuilder class. */
trait BuilderConfigurationTrait
{
    /**
     * Dimension.
     *
     * @param CoordinateDimensionEnum $dimension dimension value
     */
    public function dimension(CoordinateDimensionEnum $dimension): self
    {
        $copy = clone $this;
        $copy->coordinateDimension = $dimension;

        return $copy;
    }

    /** Return a copy in the selected spatial family. */
    /**
     * Family.
     *
     * @param SpatialModelEnum $family family value
     */
    public function family(SpatialModelEnum $family): self
    {
        $copy = clone $this;
        $copy->spatialFamily = $family;

        return $copy;
    }

    /** Return a copy configured for immutable output. */
    /**
     * Immutable.
     */
    public function immutable(): self
    {
        $copy = clone $this;
        $copy->isMutable = false;

        return $copy;
    }

    /**
     * Mutable.
     */
    public function mutable(): self
    {
        $copy = clone $this;
        $copy->isMutable = true;

        return $copy;
    }

    /** Create one point using the selected layout; XYM uses the named m argument. */
    /**
     * Srid.
     *
     * @param int|SpatialReference $srid srid value
     */
    public function srid(int|SpatialReference $srid): self
    {
        $copy = clone $this;
        $copy->reference = \is_int($srid) ? SpatialReference::fromSrid($srid) : $srid;

        return $copy;
    }
}
