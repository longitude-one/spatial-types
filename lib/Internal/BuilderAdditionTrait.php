<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Internal;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
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
     * @param SpatialInterface $curve curve value
     */
    public function addCurve(SpatialInterface $curve): self
    {
        if (!$curve instanceof CircularStringInterface && !$curve instanceof LineStringInterface) {
            throw new InvalidValueException('addCurve() requires CircularStringInterface or LineStringInterface components.');
        }
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
     * @param SpatialInterface $lineString lineString value
     */
    public function addLineString(SpatialInterface $lineString): self
    {
        if (!$lineString instanceof LineStringInterface) {
            throw new InvalidValueException('addLineString() requires LineStringInterface components.');
        }
        $this->requireAggregate(['MultiLineString']);

        return $this->addComponent($lineString);
    }

    /**
     * Add patch.
     *
     * @param SpatialInterface $patch patch value
     */
    public function addPatch(SpatialInterface $patch): self
    {
        if (!$patch instanceof PolygonInterface) {
            throw new InvalidValueException('addPatch() requires PolygonInterface components.');
        }
        $this->requireAggregate(['PolyhedralSurface']);

        return $this->addComponent($patch);
    }

    /** Add a newly created point to a point-based aggregate. */
    /**
     * Add point.
     *
     * @param float|int|string|null $x x value
     * @param float|int|string|null $y y value
     * @param float|int|null        $z z value
     * @param float|int|null        $m m value
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
     * @param SpatialInterface $point point value
     */
    public function addPointComponent(SpatialInterface $point): self
    {
        if (!$point instanceof PointInterface) {
            throw new InvalidValueException('addPointComponent() requires PointInterface components.');
        }
        $this->requireAggregate(['LineString', 'CircularString', 'MultiPoint']);

        return $this->addComponent($point);
    }

    /**
     * Add polygon.
     *
     * @param SpatialInterface $polygon polygon value
     */
    public function addPolygon(SpatialInterface $polygon): self
    {
        if (!$polygon instanceof PolygonInterface) {
            throw new InvalidValueException('addPolygon() requires PolygonInterface components.');
        }
        $this->requireAggregate(['MultiPolygon']);

        return $this->addComponent($polygon);
    }

    /**
     * Add ring.
     *
     * @param SpatialInterface $ring ring value
     */
    public function addRing(SpatialInterface $ring): self
    {
        if (!$ring instanceof LineStringInterface) {
            throw new InvalidValueException('addRing() requires LineStringInterface components.');
        }
        $this->requireAggregate(['Polygon', 'Triangle']);

        return $this->addComponent($ring);
    }

    // Build a new spatial object while retaining the accumulated components.
}
