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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension2\Geometry;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable as GeometricPolygon;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractCollectionImmutable
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable
 */
class GeometryCollectionTest extends TestCase
{
    /**
     * Test that construction accepts another GeometryCollection.
     *
     * @see https://github.com/longitude-one/spatial-types/issues/1
     */
    public function testAddElementWithGeometryCollection(): void
    {
        $nestedCollection = new GeometryCollectionImmutable();
        $geometryCollection = new GeometryCollectionImmutable(0, [$nestedCollection]);

        static::assertSame([$nestedCollection], $geometryCollection->getElements());
    }

    /**
     * Test the get Elements method.
     */
    public function testGetElements(): void
    {
        $polygon = new GeometricPolygon([]);
        $geometryCollection = new GeometryCollectionImmutable(0, [$polygon]);
        static::assertSame([$polygon], $geometryCollection->getElements());
    }

    /**
     * Test the type getter.
     */
    public function testGetType(): void
    {
        static::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, (new GeometryCollectionImmutable())->getType());
    }

    /**
     * Test the hasElement method.
     */
    public function testHasElement(): void
    {
        $polygon = new GeometricPolygon([]);
        $geometryCollection = new GeometryCollectionImmutable(0, [$polygon]);
        static::assertTrue($geometryCollection->hasElement($polygon));
    }

    /**
     * Test the isEmpty method.
     */
    public function testIsEmpty(): void
    {
        $polygon = new GeometricPolygon([]);
        static::assertTrue((new GeometryCollectionImmutable())->isEmpty());
        $geometryCollection = new GeometryCollectionImmutable(0, [$polygon]);
        static::assertFalse($geometryCollection->isEmpty());
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        $polygon = new GeometricPolygon([], 4326);
        static::assertSame([], (new GeometryCollectionImmutable())->toArray());
        $geometryCollection = new GeometryCollectionImmutable(4326, [$polygon, new PointImmutable(1, 2, 4326)]);
        static::assertSame([[], [1, 2]], $geometryCollection->toArray());
    }
}
