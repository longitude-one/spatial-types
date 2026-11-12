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

/** @internal Methods of the one public SpatialBuilder class. */
trait BuilderSelectionTrait
{
    /**
     * Circular string.
     */
    public function circularString(): self
    {
        return $this->select('CircularString');
    }

    /**
     * Collection.
     */
    public function collection(): self
    {
        return $this->select('Collection');
    }

    /**
     * Compound curve.
     */
    public function compoundCurve(): self
    {
        return $this->select('CompoundCurve');
    }

    /**
     * Line string.
     */
    public function lineString(): self
    {
        return $this->select('LineString');
    }

    /**
     * Multi line string.
     */
    public function multiLineString(): self
    {
        return $this->select('MultiLineString');
    }
}
