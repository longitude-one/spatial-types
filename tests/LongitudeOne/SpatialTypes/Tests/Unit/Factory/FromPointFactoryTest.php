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
use LongitudeOne\SpatialTypes\Factory\FromPointFactory;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as GeometricPoint;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\FromPointFactory
 */
class FromPointFactoryTest extends TestCase
{
    /**
     * Verifies that an empty point list creates an empty line string with the default geometry family.
     */
    public function testCreateEmptyLineString(): void
    {
        $lineString = FromPointFactory::createLineString([]);

        static::assertCount(0, $lineString->getPoints());
        static::assertSame(FamilyEnum::GEOMETRY, $lineString->getFamily());
        static::assertTrue($lineString->isEmpty());
    }

    /**
     * Verifies that a list of geographic points is converted into a line string with the expected SRID and family.
     */
    public function testCreateLineString(): void
    {
        $lineString = FromPointFactory::createLineString([
            new GeographicPoint(1, 2),
            new GeographicPoint(3, 4),
        ], 4326, FamilyEnum::GEOGRAPHY);

        static::assertCount(2, $lineString->getPoints());
        static::assertSame(4326, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $lineString->getFamily());
        static::assertFalse($lineString->isEmpty());
    }

    /**
     * Verifies that an invalid point entry raises an invalid value exception.
     */
    public function testCreateLineStringRejectsInvalidPoint(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('The array must only contain objects implementing PointInterface.');

        // @phpstan-ignore-next-line
        FromPointFactory::createLineString(['not a point']);
    }

    /**
     * Verifies that an unsupported dimension raises an invalid dimension exception.
     */
    public function testCreateLineStringRejectsUnsupportedDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('Only the two-dimensions points are yet supported.');

        FromPointFactory::createLineString([new GeometricPoint(1, 2)], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z);
    }
}
