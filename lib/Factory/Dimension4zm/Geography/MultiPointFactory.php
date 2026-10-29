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

namespace LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Factory\Internal\RepresentationFactory;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint;

/** Creates Dimension4zm Geography MultiPoint objects in the shared mode. */
final class MultiPointFactory
{
    /**
     * Create a spatial object using the current shared representation.
     *
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2?: null|float|int, 3?: null|float|int}|ImmutablePointInterface|MutablePointInterface> $points Constructor-compatible components or coordinate arrays
     * @param int|SpatialReference                                                                                                                                   $srid   Declared reference
     */
    public static function create(array $points, int|SpatialReference $srid = 0): MultiPoint|MultiPointImmutable
    {
        return RepresentationFactory::create(
            MultiPointImmutable::class,
            MultiPoint::class,
            [$points, $srid],
            [$points, $srid, SpatialModelEnum::GEOGRAPHY]
        );
    }
}
