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

use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon as GeometricPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point as GeometricPoint3D;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractCollection
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection
 */
class GeometryCollectionTest extends TestCase
{
    /**
     * Test that construction rejects another GeometryCollection.
     */
    public function testAddElementWithGeometryCollection(): void
    {
        static::expectException(InvalidValueException::class);
        static::expectExceptionMessageIsOrContains('An instance of LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection cannot contain another GeometryCollection nor GeographyCollection.');
        new GeometryCollection(0, [new GeometryCollection()]);
    }

    /**
     * Test that construction rejects an element with a different dimension.
     */
    public function testAddElementWithInvalidDimension(): void
    {
        static::expectException(InvalidDimensionException::class);
        static::expectExceptionMessageIsOrContains('Collection cannot contain elements with different dimensions.');
        new GeometryCollection(0, [new GeometricPoint3D(0, 1, 2)]);
    }

    /**
     * Test that construction rejects an element with a different family.
     */
    public function testAddElementWithInvalidFamily(): void
    {
        static::expectException(InvalidFamilyException::class);
        static::expectExceptionMessageIsOrContains('Collection cannot contain elements with different families.');
        new GeometryCollection(0, [new GeographicPolygon([])]);
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different SRIDs.
     */
    public function testAddElementWithInvalidSrid(): void
    {
        static::expectException(InvalidSridException::class);
        static::expectExceptionMessageIsOrContains('collection member spatial reference is not compatible');

        $polygon = new GeometricPolygon([], 4327);
        new GeometryCollection(4326, [new GeometricPolygon([], 4326), $polygon]);
    }

    /**
     * Test the get Elements method.
     */
    public function testGetElements(): void
    {
        $polygon = new GeometricPolygon([]);
        $geometryCollection = new GeometryCollection(0, [$polygon]);
        static::assertSame([$polygon], $geometryCollection->getElements());
    }

    /**
     * Test the type getter.
     */
    public function testGetType(): void
    {
        static::assertSame(TypeEnum::COLLECTION, (new GeometryCollection())->getType());
    }

    /**
     * Test the hasElement method.
     */
    public function testHasElement(): void
    {
        $polygon = new GeometricPolygon([]);
        $geometryCollection = new GeometryCollection(0, [$polygon]);
        static::assertTrue($geometryCollection->hasElement($polygon));
    }

    /**
     * Test the isEmpty method.
     */
    public function testIsEmpty(): void
    {
        $polygon = new GeometricPolygon([]);
        static::assertTrue((new GeometryCollection())->isEmpty());
        $geometryCollection = new GeometryCollection(0, [$polygon]);
        static::assertFalse($geometryCollection->isEmpty());
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        $polygon = new GeometricPolygon([], 4326);
        static::assertSame([], (new GeometryCollection())->toArray());
        $geometryCollection = new GeometryCollection(4326, [$polygon, new Point(1, 2, 4326)]);
        static::assertSame([[], [1, 2]], $geometryCollection->toArray());
    }
}
