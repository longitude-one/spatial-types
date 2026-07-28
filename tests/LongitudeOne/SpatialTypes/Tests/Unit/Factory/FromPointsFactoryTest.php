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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\FromPointsFactory;
use LongitudeOne\SpatialTypes\Types\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Geometry\Point as GeometricPoint;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\FromPointsFactory
 */
class FromPointsFactoryTest extends TestCase
{
    public function testCreateLineString(): void
    {
        $lineString = FromPointsFactory::createLineString([
            new GeographicPoint(1, 2),
            new GeographicPoint(3, 4),
        ], 4326, FamilyEnum::GEOGRAPHY);

        static::assertCount(2, $lineString->getPoints());
        static::assertSame(4326, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $lineString->getFamily());
        static::assertFalse($lineString->isEmpty());
    }

    public function testCreateEmptyLineString(): void
    {
        $lineString = FromPointsFactory::createLineString([]);

        static::assertCount(0, $lineString->getPoints());
        static::assertSame(FamilyEnum::GEOMETRY, $lineString->getFamily());
        static::assertTrue($lineString->isEmpty());
    }

    public function testCreateLineStringRejectsUnsupportedDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('Only the two-dimensions points are yet supported.');

        FromPointsFactory::createLineString([new GeometricPoint(1, 2)], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z);
    }

    public function testCreateLineStringRejectsInvalidPoint(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('The array must only contain objects implementing PointInterface.');

        FromPointsFactory::createLineString(['not a point']);
    }
}
