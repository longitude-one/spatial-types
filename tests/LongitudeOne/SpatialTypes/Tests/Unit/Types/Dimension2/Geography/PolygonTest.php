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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension2\Geography;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the two-dimensional geographic polygon.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Boundary\PolygonBoundary
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon
 */
class PolygonTest extends TestCase
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
        new Polygon([[[1, 2], [3, 4], [93, 186], [1, 2]]]);
    }

    /**
     * Test the constructor with geodesic points.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithGeodesicPoints(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('g');
        new Polygon([[['240W', '340S'], ['45W', '45N'], ['45W', '90N'], ['240W', '340S']]]);
    }

    /**
     * Test the getDimension method.
     */
    public function testGetDimension(): void
    {
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertFalse($polygon->hasZ());
        static::assertFalse($polygon->hasM());
        $ring = $polygon->getRing(0);
        static::assertFalse($ring->hasZ());
        static::assertFalse($ring->hasM());
        foreach ($ring->getPoints() as $point) {
            static::assertFalse($point->hasZ());
            static::assertFalse($point->hasM());
        }
    }

    /**
     * Test the getFamily method.
     */
    public function testGetFamily(): void
    {
        $expected = SpatialModelEnum::GEOGRAPHY;
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertSame($expected, $polygon->getFamily());
        $ring = $polygon->getRing(0);
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
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertSame(TypeEnum::POLYGON, $polygon->getType());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]]);
        static::assertSame('{"type":"Polygon","coordinates":[[[1,2],[3,4],[3,6],[1,2]]],"srid":0}', json_encode($polygon));
        $polygon = new Polygon([[[1, 2], [3, 4], [3, 6], [1, 2]]], 4326);
        static::assertSame('{"type":"Polygon","coordinates":[[[1,2],[3,4],[3,6],[1,2]]],"srid":4326}', json_encode($polygon));
    }
}
