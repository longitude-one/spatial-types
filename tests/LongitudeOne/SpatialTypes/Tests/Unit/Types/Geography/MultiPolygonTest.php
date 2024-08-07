<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Geography;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Geography\MultiPolygon;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of geometric line string.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Geography\MultiPolygon
 */
class MultiPolygonTest extends TestCase
{
    /**
     * Test the constructor with array of cartesian coordinates.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithCartesianCoordinates(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('Out of range latitude value, latitude must be between -90 and 90, got "186".');
        new MultiPolygon([[[[1, 2], [3, 4], [93, 186], [1, 2]]]]);
    }

    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('Out of range longitude value, longitude must be between -180 and 180, got "240W".');
        new MultiPolygon([[[['240W', '340S'], ['45W', '45N'], ['45W', '90N'], ['240W', '340S']]]]);
    }

    /**
     * Test the getDimension method.
     */
    public function testGetDimension(): void
    {
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]]);
        static::assertFalse($multiPolygon->hasZ());
        static::assertFalse($multiPolygon->hasM());
        $polygon = $multiPolygon->getPolygon(0);
        static::assertFalse($polygon->hasZ());
        static::assertFalse($polygon->hasM());
        foreach ($polygon->getRings() as $ring) {
            static::assertFalse($ring->hasZ());
            static::assertFalse($ring->hasM());
        }
    }

    /**
     * Test the getFamily method.
     */
    public function testGetFamily(): void
    {
        $expected = FamilyEnum::GEOGRAPHY;
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]]);
        static::assertSame($expected, $multiPolygon->getFamily());
        $polygon = $multiPolygon->getPolygon(0);
        static::assertSame($expected, $polygon->getFamily());
        foreach ($polygon->getRings() as $ring) {
            static::assertSame($expected, $ring->getFamily());
        }
    }

    /**
     * Test the getTypes method.
     */
    public function testGetType(): void
    {
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]]);
        static::assertSame('MultiPolygon', $multiPolygon->getType());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]]);
        static::assertSame('{"type":"MultiPolygon","coordinates":[[[[1,2],[3,4],[3,6],[1,2]]]],"srid":null}', json_encode($multiPolygon));
        $multiPolygon = new MultiPolygon([[[[1, 2], [3, 4], [3, 6], [1, 2]]]], 4326);
        static::assertSame('{"type":"MultiPolygon","coordinates":[[[[1,2],[3,4],[3,6],[1,2]]]],"srid":4326}', json_encode($multiPolygon));
    }
}
