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

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Collection\AbstractPointCollection;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Abstract MultiPoint class.
 *
 * @internal this class provides common behaviour for geometry and geography multi-points
 */
abstract class AbstractMultiPoint extends AbstractPointCollection implements MultiPointInterface
{
    /**
     * AbstractMultiPoint constructor.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface)[] $points points of the multipoint
     * @param int|SpatialReference                                                                                         $srid   Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the multipoint dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the multipoint SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    abstract public function __construct(array $points, int|SpatialReference $srid = 0);

    /**
     * Get the elements (the points) of this multipoint.
     *
     * @return PointInterface[]
     */
    public function getElements(): array
    {
        return $this->getPoints();
    }

    /**
     * Return a deep copy of this multi-point with one replacement point.
     *
     * The original multi-point and all its points remain unchanged. The
     * replacement coordinates are validated by the selected point, which keeps
     * the multi-point's family, dimension, and Spatial Reference Identifier
     * (SRID) unchanged.
     *
     * @param int         $pointIndex  index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates replacement point coordinates
     *
     * @throws OutOfBoundsException when the multi-point has no points
     */
    public function withPoint(int $pointIndex, Coordinates $coordinates): static
    {
        $pointIndex = $this->normalizePointIndex($pointIndex);
        $multiPoint = clone $this;
        $multiPoint->points = array_map(
            static fn (PointInterface $point): PointInterface => $point->withSrid($point->getSrid()),
            $this->points
        );
        $multiPoint->points[$pointIndex] = $multiPoint->points[$pointIndex]->withCoordinates($coordinates);

        return $multiPoint;
    }

    /**
     * Return a deep copy declared in the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $multiPoint = parent::withSpatialReference($reference);
        $multiPoint->points = array_map(
            static fn (PointInterface $point): PointInterface => $point->withSpatialReference($reference),
            $this->points
        );

        return $multiPoint;
    }

    /**
     * Return a copy of this multipoint with the given Spatial Reference Identifier (SRID).
     *
     * Every point is copied with the requested SRID to preserve the aggregate's
     * internal SRID consistency.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        return $this->withSpatialReference(SpatialReference::fromSrid($srid));
    }

    /**
     * Normalize a point index according to the multi-point accessor convention.
     *
     * @param int $pointIndex point index to normalize
     *
     * @throws OutOfBoundsException when the multi-point has no points
     */
    private function normalizePointIndex(int $pointIndex): int
    {
        $pointCount = count($this->points);
        if (0 === $pointCount) {
            throw new OutOfBoundsException('The current collection of points is empty.');
        }

        $pointIndex %= $pointCount;

        return $pointIndex < 0 ? $pointCount + $pointIndex : $pointIndex;
    }
}
