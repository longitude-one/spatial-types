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

namespace LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geometry;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Factory\RepresentationFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\TriangleImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Triangle;

/** Creates Dimension4zm Geometry Triangle objects in the shared mode. */
final class TriangleFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}|ImmutablePointInterface|MutablePointInterface>|ImmutableLineStringInterface|MutableLineStringInterface> $rings Constructor-compatible components or coordinate arrays
     * @param int|SpatialReference                                                                                                                                                                                                  $srid  Declared reference
     */
    public static function create(array $rings, int|SpatialReference $srid = 0): Triangle|TriangleImmutable
    {
        return RepresentationFactory::create(
            TriangleImmutable::class,
            Triangle::class,
            [$rings, $srid],
            [$rings, $srid, SpatialModelEnum::GEOMETRY]
        );
    }
}
