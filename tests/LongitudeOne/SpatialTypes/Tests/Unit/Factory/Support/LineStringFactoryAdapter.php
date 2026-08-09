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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory\Support;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Factory\DefaultSpatialFactoryFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/**
 * Adapts line string factory behavior tests to SpatialFactory.
 *
 * @internal
 */
final class LineStringFactoryAdapter
{
    /**
     * @param PointInterface[] $points points
     */
    public static function fromArrayOfPoints(array $points, int $srid = SpatialInterface::DEFAULT_SRID, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): LineStringInterface
    {
        return DefaultSpatialFactoryFactory::create()->createLineString($points, new SpatialContext($srid, $family, $dimension));
    }

    /**
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[]|PointInterface[] $points points
     */
    public static function fromIndexedArray(array $points, int $srid = SpatialInterface::DEFAULT_SRID, FamilyEnum $family = FamilyEnum::GEOMETRY, DimensionEnum $dimension = DimensionEnum::X_Y): LineStringInterface
    {
        return DefaultSpatialFactoryFactory::create()->createLineStringFromIndexedArray($points, new SpatialContext($srid, $family, $dimension));
    }
}
