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

namespace LongitudeOne\SpatialTypes\Factory\Hydrator;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Resolver\SpatialFamilyFactoryResolver;

/**
 * Hydrates spatial components from nested indexed arrays.
 */
final class SpatialArrayHydrator
{
    /**
     * @param CoordinatesHydrator $coordinatesHydrator hydrator for individual points
     */
    public function __construct(private CoordinatesHydrator $coordinatesHydrator = new CoordinatesHydrator())
    {
    }

    /**
     * Hydrate the points of a line string.
     *
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface> $points  points
     * @param SpatialContext                                                                                                  $context family, dimension, and SRID expected for every point
     *
     * @return PointInterface[]
     *
     * @throws InvalidValueException when a point has an unsupported representation
     */
    public function hydrateLineString(array $points, SpatialContext $context): array
    {
        $hydratedPoints = [];
        foreach ($points as $point) {
            if (!is_array($point) && !$point instanceof PointInterface) {
                throw new InvalidValueException('The array must contain only objects implementing PointInterface or array of coordinates.');
            }

            if ($point instanceof PointInterface) {
                $hydratedPoints[] = $point;

                continue;
            }

            $hydratedPoints[] = SpatialFamilyFactoryResolver::resolvePointFactory($context->family)->create(
                $this->coordinatesHydrator->hydrate($point, $context),
                $context
            );
        }

        return $hydratedPoints;
    }

    /**
     * Hydrate the rings of a polygon.
     *
     * @param array<array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface>|LineStringInterface> $rings   rings
     * @param SpatialContext                                                                                                                             $context family, dimension, and SRID expected for every ring
     *
     * @return LineStringInterface[]
     *
     * @throws InvalidValueException when a ring has an unsupported representation
     */
    public function hydratePolygon(array $rings, SpatialContext $context): array
    {
        $lineStrings = [];
        foreach ($rings as $ring) {
            if (!is_array($ring) && !$ring instanceof LineStringInterface) {
                throw new InvalidValueException('The array must contain only objects implementing LineStringInterface or array of PointInterface or "array of array of coordinates".');
            }

            if ($ring instanceof LineStringInterface) {
                $lineStrings[] = $ring;

                continue;
            }

            $lineStrings[] = SpatialFamilyFactoryResolver::resolveLineStringFactory($context->family)->create(
                $this->hydrateLineString($ring, $context),
                $context
            );
        }

        return $lineStrings;
    }
}
