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
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the three-dimensional geographic polygon.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Trait\LineStringTrait
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialType
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Polygon
 */
class PolygonTest extends TestCase
{
    private const DEFAULT_COORDINATES = [[[1, 2, -3], [3, 4, 5], [3, 6, 42.195], [1, 2, -3]]];

    /**
     * Test the constructor with array of cartesian coordinates.
     *
     * @throws SpatialTypeExceptionInterface test shall not throw any exception
     */
    public function testConstructorWithCartesianCoordinates(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Out of range latitude value, latitude must be between -90 and 90, got "186".');
        new Polygon([[[1, 2, -3], [3, 4, 5], [93, 186, 0], [1, 2, -3]]]);
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
        new Polygon([[['240W', '340S', -3], ['45W', '45N', 5], ['45W', '90N', 0], ['240W', '340S', -3]]]);
    }

    /**
     * Test the getDimension method.
     */
    public function testGetDimension(): void
    {
        $polygon = new Polygon(self::DEFAULT_COORDINATES);
        static::assertTrue($polygon->hasZ());
        static::assertFalse($polygon->hasM());
        $ring = $polygon->getRing(0);
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
        $polygon = new Polygon(self::DEFAULT_COORDINATES);
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
        $polygon = new Polygon(self::DEFAULT_COORDINATES);
        static::assertSame(TypeEnum::POLYGON, $polygon->getType());
    }

    /**
     * Test the json serialize.
     */
    public function testJsonSerialize(): void
    {
        $polygon = new Polygon(self::DEFAULT_COORDINATES);
        static::assertSame('{"type":"Polygon","coordinates":[[[1,2,-3],[3,4,5],[3,6,42.195],[1,2,-3]]],"srid":0}', json_encode($polygon));
        $polygon = new Polygon(self::DEFAULT_COORDINATES, 4326);
        static::assertSame('{"type":"Polygon","coordinates":[[[1,2,-3],[3,4,5],[3,6,42.195],[1,2,-3]]],"srid":4326}', json_encode($polygon));
    }
}
