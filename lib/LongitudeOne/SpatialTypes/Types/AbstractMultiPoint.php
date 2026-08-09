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
use LongitudeOne\SpatialTypes\Interfaces\MultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Trait\PointTrait;

/**
 * Abstract MultiPoint class.
 *
 * @internal this class provides common behaviour for geometry and geography multi-points
 */
abstract class AbstractMultiPoint extends AbstractSpatialType implements MultiPointInterface
{
    use PointTrait;

    /**
     * AbstractMultiPoint constructor.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface)[] $points points of the multipoint
     * @param int                                                                                                          $srid   Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the multipoint dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the multipoint SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    abstract public function __construct(array $points, int $srid = self::DEFAULT_SRID);

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
     * Return a copy of this multipoint with the given Spatial Reference Identifier (SRID).
     *
     * Every point is copied with the requested SRID to preserve the aggregate's
     * internal SRID consistency.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        $multiPoint = parent::withSrid($srid);
        $multiPoint->points = array_map(
            static fn (PointInterface $point): PointInterface => $point->withSrid($srid),
            $this->points
        );

        return $multiPoint;
    }
}
