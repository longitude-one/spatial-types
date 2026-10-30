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
trait BuilderMoreSelectionTrait
{
    /**
     * Multi point.
     */
    public function multiPoint(): self
    {
        return $this->select('MultiPoint');
    }

    /**
     * Multi polygon.
     */
    public function multiPolygon(): self
    {
        return $this->select('MultiPolygon');
    }

    /** Return a copy configured for mutable output. */
    /**
     * Polygon.
     */
    public function polygon(): self
    {
        return $this->select('Polygon');
    }

    /**
     * Polyhedral surface.
     */
    public function polyhedralSurface(): self
    {
        return $this->select('PolyhedralSurface');
    }

    /** Return a copy with the declared spatial reference. */
    /**
     * Triangle.
     */
    public function triangle(): self
    {
        return $this->select('Triangle');
    }
}
