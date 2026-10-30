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

use LongitudeOne\SpatialTypes\Interfaces\CircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/** @internal Methods of the one public SpatialBuilder class. */
trait BuilderAdditionTrait
{
    /**
     * Add curve.
     *
     * @param CircularStringInterface|LineStringInterface $curve curve value
     */
    public function addCurve(CircularStringInterface|LineStringInterface $curve): self
    {
        $this->requireAggregate(['CompoundCurve']);

        return $this->addComponent($curve);
    }

    /**
     * Add element.
     *
     * @param SpatialInterface $element element value
     */
    public function addElement(SpatialInterface $element): self
    {
        $this->requireAggregate(['Collection']);

        return $this->addComponent($element);
    }

    /**
     * Add line string.
     *
     * @param LineStringInterface $lineString lineString value
     */
    public function addLineString(LineStringInterface $lineString): self
    {
        $this->requireAggregate(['MultiLineString']);

        return $this->addComponent($lineString);
    }

    /**
     * Add patch.
     *
     * @param PolygonInterface $patch patch value
     */
    public function addPatch(PolygonInterface $patch): self
    {
        $this->requireAggregate(['PolyhedralSurface']);

        return $this->addComponent($patch);
    }

    /** Add a newly created point to a point-based aggregate. */
    /**
     * Add point.
     *
     * @param null|float|int|string $x x value
     * @param null|float|int|string $y y value
     * @param null|float|int        $z z value
     * @param null|float|int        $m m value
     */
    public function addPoint(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $z = null, float|int|null $m = null): self
    {
        $this->requireAggregate(['LineString', 'CircularString', 'MultiPoint']);

        return $this->addComponent($this->point($x, $y, $z, $m));
    }

    /** Retain the supplied point without copying it. */
    /**
     * Add point component.
     *
     * @param PointInterface $point point value
     */
    public function addPointComponent(PointInterface $point): self
    {
        $this->requireAggregate(['LineString', 'CircularString', 'MultiPoint']);

        return $this->addComponent($point);
    }

    /**
     * Add polygon.
     *
     * @param PolygonInterface $polygon polygon value
     */
    public function addPolygon(PolygonInterface $polygon): self
    {
        $this->requireAggregate(['MultiPolygon']);

        return $this->addComponent($polygon);
    }

    /**
     * Add ring.
     *
     * @param LineStringInterface $ring ring value
     */
    public function addRing(LineStringInterface $ring): self
    {
        $this->requireAggregate(['Polygon', 'Triangle']);

        return $this->addComponent($ring);
    }

    // Build a new spatial object while retaining the accumulated components.
}
