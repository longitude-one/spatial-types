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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3z\Geography;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineString;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the three-dimensional geographic multi-line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Collection\AbstractLineStringCollection
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineString
 */
class MultiLineStringTest extends TestCase
{
    /**
     * Test the constructor with array of cartesian coordinates.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithCartesianCoordinates(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Out of range latitude value, latitude must be between -90 and 90, got "186".');
        new MultiLineString([[[1, 2, 3], [3, 4, 5], [93, 186, 42.2], [10, 20, 30]]]);
    }

    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Out of range longitude value, longitude must be between -180 and 180, got "240W".');
        new MultiLineString([[['240W', '340S', 0], ['45W', '45N', -1], ['45W', '90N', 42.195]]]);
    }

    /**
     * Test the getDimension method.
     */
    public function testGetDimension(): void
    {
        $multiLineString = new MultiLineString([[[1, 2, -3], [3, 4, 5], [3, 6, 42.195], [1, 2, -3]]]);
        static::assertTrue($multiLineString->hasZ());
        static::assertFalse($multiLineString->hasM());
        $ring = $multiLineString->getLineString(0);
        static::assertTrue($ring->hasZ());
        static::assertFalse($ring->hasM());
        foreach ($ring->getPoints() as $point) {
            static::assertTrue($point->hasZ());
            static::assertFalse($point->hasM());
        }
    }

    /**
     * Test the getFamily method.
     */
    public function testGetFamily(): void
    {
        $expected = FamilyEnum::GEOGRAPHY;
        $multiLineString = new MultiLineString([[[1, 2, -3], [3, 4, 5], [3, 6, 42.195], [1, 2, -3]]]);
        static::assertSame($expected, $multiLineString->getFamily());
        $ring = $multiLineString->getLineString(0);
        static::assertSame($expected, $ring->getFamily());
        foreach ($ring->getPoints() as $point) {
            static::assertSame($expected, $point->getFamily());
        }
    }

    /**
     * Test the getTypes method.
     */
    public function testGetType(): void
    {
        $multiLineString = new MultiLineString([[[1, 2, -3], [3, 4, 5], [3, 6, 42.195], [1, 2, -3]]]);
        static::assertSame(TypeEnum::MULTILINESTRING, $multiLineString->getType());
    }

    /**
     * Test the isEmpty method.
     */
    public function testIsEmpty(): void
    {
        $multiLineString = new MultiLineString([]);
        static::assertTrue($multiLineString->isEmpty());

        $multiLineString = new MultiLineString([[[1, 2, -3], [3, 4, 5], [3, 6, 42.195], [1, 2, -3]]]);
        static::assertFalse($multiLineString->isEmpty());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $multiLineString = new MultiLineString([[[1, 2, -3], [3, 4, 5], [3, 6, 42.195], [1, 2, -3]]]);
        static::assertSame('{"type":"MultiLineString","coordinates":[[[1,2,-3],[3,4,5],[3,6,42.195],[1,2,-3]]],"srid":0}', json_encode($multiLineString));
        $multiLineString = new MultiLineString([[[1, 2, -3], [3, 4, 5], [3, 6, 42.195], [1, 2, -3]]], 4326);
        static::assertSame('{"type":"MultiLineString","coordinates":[[[1,2,-3],[3,4,5],[3,6,42.195],[1,2,-3]]],"srid":4326}', json_encode($multiLineString));
    }
}
