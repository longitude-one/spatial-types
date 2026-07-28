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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Geometry;

use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Geography\Polygon as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Geometry\GeometryCollection;
use LongitudeOne\SpatialTypes\Types\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Geometry\Polygon as GeometricPolygon;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractCollection
 * @covers \LongitudeOne\SpatialTypes\Types\Geometry\GeometryCollection
 */
class GeometryCollectionTest extends TestCase
{
    /**
     * Test that the addElement method throws an exception when developers try to add another GeometryCollection.
     */
    public function testAddElementWithGeometryCollection(): void
    {
        $geometryCollection = new GeometryCollection();
        static::expectException(InvalidValueException::class);
        static::expectExceptionMessageIsOrContains('An instance of LongitudeOne\SpatialTypes\Types\Geometry\GeometryCollection cannot contain another GeometryCollection nor GeographyCollection.');
        $geometryCollection->addElement(new GeometryCollection());
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different dimensions.
     */
    public function testAddElementWithInvalidDimension(): void
    {
        $geometryCollection = new GeometryCollection();
        static::expectException(InvalidDimensionException::class);
        static::expectExceptionMessageIsOrContains('Collection cannot contain elements with different dimensions.');
        $mock = static::createStub(PolygonInterface::class);
        $mock->method('getFamily')->willReturn(FamilyEnum::GEOMETRY);
        $mock->method('hasM')->willReturn(false);
        $mock->method('hasZ')->willReturn(true);

        $geometryCollection->addElement($mock);
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different families.
     */
    public function testAddElementWithInvalidFamily(): void
    {
        $geometryCollection = new GeometryCollection();
        static::expectException(InvalidFamilyException::class);
        static::expectExceptionMessageIsOrContains('Collection cannot contain elements with different families.');
        $geometryCollection->addElement(new GeographicPolygon([]));
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different SRIDs.
     */
    public function testAddElementWithInvalidSrid(): void
    {
        $geometryCollection = new GeometryCollection();
        $polygon = new GeometricPolygon([], 4326);
        $geometryCollection->addElement($polygon);
        $polygon = new GeometricPolygon([], 4327);
        $geometryCollection->addElement($polygon);

        $geometryCollection = new GeometryCollection(4326);
        $polygon = new GeometricPolygon([], 4326);
        $geometryCollection->addElement($polygon);
        static::expectException(InvalidSridException::class);
        static::expectExceptionMessageIsOrContains('Collection cannot contain elements with different SRIDs.');

        $polygon = new GeometricPolygon([], 4327);
        $geometryCollection->addElement($polygon);
    }

    /**
     * Test the get Elements method.
     */
    public function testGetElementsAndAddElement(): void
    {
        $geometryCollection = new GeometryCollection();
        static::assertEmpty($geometryCollection->getElements());

        $polygon = new GeometricPolygon([]);
        $geometryCollection->addElement($polygon);
        static::assertSame([$polygon], $geometryCollection->getElements());
    }

    /**
     * Test the type getter.
     */
    public function testGetType(): void
    {
        static::assertSame('Collection', (new GeometryCollection())->getType());
    }

    /**
     * Test the hasElement method.
     */
    public function testHasElement(): void
    {
        $geometryCollection = new GeometryCollection();
        $polygon = new GeometricPolygon([]);
        static::assertFalse($geometryCollection->hasElement($polygon));
        $geometryCollection->addElement($polygon);
        static::assertTrue($geometryCollection->hasElement($polygon));
    }

    /**
     * Test the isEmpty method.
     */
    public function testIsEmpty(): void
    {
        $geometryCollection = new GeometryCollection();
        static::assertTrue($geometryCollection->isEmpty());
        $polygon = new GeometricPolygon([]);
        $geometryCollection->addElement($polygon);
        static::assertFalse($geometryCollection->isEmpty());
        $geometryCollection->removeElement($polygon);
        static::assertTrue($geometryCollection->isEmpty());
    }

    /**
     * Test the removeElement method.
     */
    public function testRemoveElement(): void
    {
        $geometryCollection = new GeometryCollection();
        $polygon = new GeometricPolygon([]);
        $geometryCollection->addElement($polygon);
        static::assertTrue($geometryCollection->hasElement($polygon));
        $geometryCollection->removeElement($polygon);
        static::assertFalse($geometryCollection->hasElement($polygon));
    }

    /**
     * Test that removing an element absent from the collection throws an exception.
     */
    public function testRemoveUnknownElement(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('The spatial object is not in the collection.');

        (new GeometryCollection())->removeElement(new GeometricPolygon([]));
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        $geometryCollection = new GeometryCollection();
        static::assertSame([], $geometryCollection->toArray());
        $polygon = new GeometricPolygon([]);
        $geometryCollection->addElement($polygon);
        static::assertSame([[]], $geometryCollection->toArray());
        $geometryCollection->addElement(new Point(1, 2, 4326));
        static::assertSame([[], [1, 2]], $geometryCollection->toArray());
    }
}
