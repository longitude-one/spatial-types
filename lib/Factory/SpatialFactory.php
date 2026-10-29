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
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePolygonInterface;

/**
 * Creates spatial types using injected constructors and hydrators.
 *
 * @internal this factory supports aggregate constructors that accept arrays
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
     * @param ImmutablePointInterface[] $points  points composing the line string
     * @param SpatialContext            $context family, dimension, and SRID to apply
     *
     * @throws InvalidValueException when an element is not a point
     */
    public function createLineString(array $points, SpatialContext $context): ImmutableLineStringInterface
    {
        foreach ($points as $point) {
            if (!$point instanceof ImmutablePointInterface) {
                throw new InvalidValueException('The array must contain only objects implementing ImmutablePointInterface.');
            }
        }

        return $this->factoryRegistry->lineStringFactory($context->family)->create($points, $context);
    }

    /**
     * Create a line string from an indexed array.
     *
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface> $points  points
     * @param SpatialContext                                                                                                                     $context family, dimension, and SRID to apply
     */
    public function createLineStringFromIndexedArray(array $points, SpatialContext $context): ImmutableLineStringInterface
    {
        return $this->createLineString($this->spatialArrayHydrator->hydrateLineString($points, $context), $context);
    }

    /**
     * Create a point from typed coordinates.
     *
     * @param Coordinates    $coordinates normalized X, Y, Z, and M values
     * @param SpatialContext $context     family, dimension, and SRID to apply
     *
     * @throws InvalidDimensionException when a coordinate is not supported by the requested dimension
     */
    public function createPoint(Coordinates $coordinates, SpatialContext $context): ImmutablePointInterface
    {
        if ((!$context->dimension->hasZ() && null !== $coordinates->z) || (!$context->dimension->hasM() && null !== $coordinates->m)) {
            throw new InvalidDimensionException('The third and fourth dimensions are not supported for two-dimensions points. Did you miss the 7th parameter CoordinateDimensionEnum?');
        }

        return $this->factoryRegistry->pointFactory($context->family)->create($coordinates, $context);
    }

    /**
     * Create a point from an indexed array.
     *
     * @param array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|array{} $coordinates coordinates
     * @param SpatialContext                                                                                              $context     family, dimension, and SRID to apply
     */
    public function createPointFromIndexedArray(array $coordinates, SpatialContext $context): ImmutablePointInterface
    {
        if ([] === $coordinates) {
            return $this->factoryRegistry->pointFactory($context->family)->createEmpty($context);
        }

        return $this->createPoint($this->coordinatesHydrator->hydrate($coordinates, $context), $context);
    }

    /**
     * Create a polygon from typed line strings.
     *
     * @param ImmutableLineStringInterface[] $lineStrings line strings that form polygon rings
     * @param SpatialContext                 $context     family, dimension, and SRID to apply
     *
     * @throws InvalidValueException when an element is not a line string
     */
    public function createPolygon(array $lineStrings, SpatialContext $context): ImmutablePolygonInterface
    {
        foreach ($lineStrings as $lineString) {
            if (!$lineString instanceof ImmutableLineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing ImmutableLineStringInterface.');
            }
        }

        return $this->factoryRegistry->polygonFactory($context->family)->create($lineStrings, $context);
    }

    /**
     * Create a polygon from an indexed array.
     *
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface>|ImmutableLineStringInterface> $rings   rings
     * @param SpatialContext                                                                                                                                                         $context family, dimension, and SRID to apply
     */
    public function createPolygonFromIndexedArray(array $rings, SpatialContext $context): ImmutablePolygonInterface
    {
        return $this->createPolygon($this->spatialArrayHydrator->hydratePolygon($rings, $context), $context);
    }
}
