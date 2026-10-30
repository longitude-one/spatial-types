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
