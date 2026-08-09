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

namespace LongitudeOne\SpatialTypes\Factory;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\Hydrator\CoordinatesHydrator;
use LongitudeOne\SpatialTypes\Factory\Hydrator\SpatialArrayHydrator;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;

/**
 * Creates spatial types using injected constructors and hydrators.
 */
final class SpatialFactory
{
    /**
     * @param SpatialFactoryRegistry $factoryRegistry      registry of constructors
     * @param CoordinatesHydrator    $coordinatesHydrator  hydrator for point coordinates
     * @param SpatialArrayHydrator   $spatialArrayHydrator hydrator for nested spatial arrays
     */
    public function __construct(
        private SpatialFactoryRegistry $factoryRegistry,
        private CoordinatesHydrator $coordinatesHydrator,
        private SpatialArrayHydrator $spatialArrayHydrator
    ) {
    }

    /**
     * Create a line string from typed points.
     *
     * @param PointInterface[] $points points
     *
     * @throws InvalidValueException when an element is not a point
     */
    public function createLineString(array $points, SpatialContext $context): LineStringInterface
    {
        foreach ($points as $point) {
            if (!$point instanceof PointInterface) {
                throw new InvalidValueException('The array must contain only objects implementing PointInterface.');
            }
        }

        return $this->factoryRegistry->lineStringFactory($context->family)->create($points, $context);
    }

    /**
     * Create a line string from an indexed array.
     *
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface> $points points
     */
    public function createLineStringFromIndexedArray(array $points, SpatialContext $context): LineStringInterface
    {
        return $this->createLineString($this->spatialArrayHydrator->hydrateLineString($points, $context), $context);
    }

    /**
     * Create a point from typed coordinates.
     *
     * @throws InvalidDimensionException when a coordinate is not supported by the requested dimension
     */
    public function createPoint(Coordinates $coordinates, SpatialContext $context): PointInterface
    {
        if ((!$context->dimension->hasZ() && null !== $coordinates->z) || (!$context->dimension->hasM() && null !== $coordinates->m)) {
            throw new InvalidDimensionException('The third and fourth dimensions are not supported for two-dimensions points. Did you miss the 7th parameter DimensionEnum?');
        }

        return $this->factoryRegistry->pointFactory($context->family)->create($coordinates, $context);
    }

    /**
     * Create a point from an indexed array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $coordinates coordinates
     */
    public function createPointFromIndexedArray(array $coordinates, SpatialContext $context): PointInterface
    {
        return $this->createPoint($this->coordinatesHydrator->hydrate($coordinates, $context), $context);
    }

    /**
     * Create a polygon from typed line strings.
     *
     * @param LineStringInterface[] $lineStrings line strings that form polygon rings
     *
     * @throws InvalidValueException when an element is not a line string
     */
    public function createPolygon(array $lineStrings, SpatialContext $context): PolygonInterface
    {
        foreach ($lineStrings as $lineString) {
            if (!$lineString instanceof LineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing LineStringInterface.');
            }
        }

        return $this->factoryRegistry->polygonFactory($context->family)->create($lineStrings, $context);
    }

    /**
     * Create a polygon from an indexed array.
     *
     * @param array<array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface>|LineStringInterface> $rings rings
     */
    public function createPolygonFromIndexedArray(array $rings, SpatialContext $context): PolygonInterface
    {
        return $this->createPolygon($this->spatialArrayHydrator->hydratePolygon($rings, $context), $context);
    }
}
