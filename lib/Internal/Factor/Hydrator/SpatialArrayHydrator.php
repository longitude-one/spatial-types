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

namespace LongitudeOne\SpatialTypes\Internal\Factor\Hydrator;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Factory\SpatialFactoryRegistry;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;

/**
 * Hydrates spatial components from nested indexed arrays.
 *
 * @internal this hydrator supports the internal factory pipeline
 */
final class SpatialArrayHydrator
{
    /**
     * @param SpatialFactoryRegistry $factoryRegistry     registry of constructors
     * @param CoordinatesHydrator    $coordinatesHydrator hydrator for individual points
     */
    public function __construct(
        private SpatialFactoryRegistry $factoryRegistry,
        private CoordinatesHydrator $coordinatesHydrator = new CoordinatesHydrator()
    ) {
    }

    /**
     * Hydrate the points of a line string.
     *
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface> $points  points
     * @param SpatialContext                                                                                                                     $context family, dimension, and SRID expected for every point
     *
     * @return ImmutablePointInterface[]
     *
     * @throws InvalidValueException when a point has an unsupported representation
     */
    public function hydrateLineString(array $points, SpatialContext $context): array
    {
        $hydratedPoints = [];
        foreach ($points as $point) {
            if (!is_array($point) && !$point instanceof ImmutablePointInterface) {
                throw new InvalidValueException('The array must contain only objects implementing ImmutablePointInterface or array of coordinates.');
            }

            if ($point instanceof ImmutablePointInterface) {
                $hydratedPoints[] = $point;

                continue;
            }

            if ([] === $point) {
                $hydratedPoints[] = $this->factoryRegistry->pointFactory($context->family)->createEmpty($context);

                continue;
            }

            $hydratedPoints[] = $this->factoryRegistry->pointFactory($context->family)->create(
                $this->coordinatesHydrator->hydrate($point, $context),
                $context
            );
        }

        return $hydratedPoints;
    }

    /**
     * Hydrate the rings of a polygon.
     *
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface>|ImmutableLineStringInterface> $rings   rings
     * @param SpatialContext                                                                                                                                                         $context family, dimension, and SRID expected for every ring
     *
     * @return ImmutableLineStringInterface[]
     *
     * @throws InvalidValueException when a ring has an unsupported representation
     */
    public function hydratePolygon(array $rings, SpatialContext $context): array
    {
        $lineStrings = [];
        foreach ($rings as $ring) {
            if (!is_array($ring) && !$ring instanceof ImmutableLineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing ImmutableLineStringInterface or array of ImmutablePointInterface or "array of array of coordinates".');
            }

            if ($ring instanceof ImmutableLineStringInterface) {
                $lineStrings[] = $ring;

                continue;
            }

            $lineStrings[] = $this->factoryRegistry->lineStringFactory($context->family)->create(
                $this->hydrateLineString($ring, $context),
                $context
            );
        }

        return $lineStrings;
    }
}
