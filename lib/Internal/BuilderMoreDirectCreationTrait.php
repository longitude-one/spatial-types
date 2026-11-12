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
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Interfaces\MultiPolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Interfaces\TriangleInterface;

/** @internal Methods of the one public SpatialBuilder class. */
trait BuilderMoreDirectCreationTrait
{
    /** @param array<mixed> $polygons */
    /**
     * Create multi polygon.
     *
     * @param array $polygons polygons value
     *
     * @phpstan-param array<mixed> $polygons
     */
    public function createMultiPolygon(array $polygons): MultiPolygonInterface
    {
        return $this->createType('MultiPolygon', [$polygons], MultiPolygonInterface::class);
    }

    /** @param array<mixed> $rings */
    /**
     * Create polygon.
     *
     * @param array $rings rings value
     *
     * @phpstan-param array<mixed> $rings
     */
    public function createPolygon(array $rings): PolygonInterface
    {
        return $this->createType('Polygon', [$rings], PolygonInterface::class);
    }

    /** @param array<mixed> $patches */
    /**
     * Create polyhedral surface.
     *
     * @param array $patches patches value
     *
     * @phpstan-param array<mixed> $patches
     */
    public function createPolyhedralSurface(array $patches = []): PolyhedralSurfaceInterface
    {
        return $this->createType('PolyhedralSurface', [$patches], PolyhedralSurfaceInterface::class);
    }

    /** @param array<mixed> $rings */
    /**
     * Create triangle.
     *
     * @param array $rings rings value
     *
     * @phpstan-param array<mixed> $rings
     */
    public function createTriangle(array $rings): TriangleInterface
    {
        return $this->createType('Triangle', [$rings], TriangleInterface::class);
    }

    /** Return a copy in the selected coordinate layout. */
    /**
     * Point.
     *
     * @param float|int|string|null $x x value
     * @param float|int|string|null $y y value
     * @param float|int|null        $z z value
     * @param float|int|null        $m m value
     */
    public function point(float|int|string|null $x = null, float|int|string|null $y = null, float|int|null $z = null, float|int|null $m = null): PointInterface
    {
        if (null !== $z && !$this->coordinateDimension->hasZ() || null !== $m && !$this->coordinateDimension->hasM()) {
            throw new InvalidDimensionException('A non-null ordinate is unavailable in the selected coordinate layout.');
        }

        $coordinates = match ($this->coordinateDimension) {
            CoordinateDimensionEnum::XY => [$x, $y],
            CoordinateDimensionEnum::XYZ => [$x, $y, $z],
            CoordinateDimensionEnum::XYM => [$x, $y, $m],
            CoordinateDimensionEnum::XYZM => [$x, $y, $z, $m],
        };

        return $this->createType('Point', $coordinates, PointInterface::class);
    }
}
