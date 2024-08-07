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
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Types\Geography\GeographyCollection;
use LongitudeOne\SpatialTypes\Types\Geography\Point;
use LongitudeOne\SpatialTypes\Types\Geography\Polygon as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Geometry\Polygon as GeometricPolygon;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractCollection
 * @covers \LongitudeOne\SpatialTypes\Types\Geography\GeographyCollection
 */
class GeographyCollectionTest extends TestCase
{
    /**
     * Test that the addElement method throws an exception when developers try to add another GeographyCollection.
     */
    public function testAddElementWithGeographyCollection(): void
    {
        $geographyCollection = new GeographyCollection();
        static::expectException(InvalidValueException::class);
        static::expectExceptionMessage('An instance of LongitudeOne\SpatialTypes\Types\Geography\GeographyCollection cannot contain another GeometryCollection nor GeographyCollection.');
        $geographyCollection->addElement(new GeographyCollection());
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different dimensions.
     */
    public function testAddElementWithInvalidDimension(): void
    {
        $geographyCollection = new GeographyCollection();
        static::expectException(InvalidDimensionException::class);
        static::expectExceptionMessage('Collection cannot contain elements with different dimensions.');
        $mock = $this->createMock(PolygonInterface::class);
        $mock->method('getFamily')->willReturn(FamilyEnum::GEOGRAPHY);
        $mock->method('hasM')->willReturn(false);
        $mock->method('hasZ')->willReturn(true);

        $geographyCollection->addElement($mock);
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different families.
     */
    public function testAddElementWithInvalidFamily(): void
    {
        $geographyCollection = new GeographyCollection();
        static::expectException(InvalidFamilyException::class);
        static::expectExceptionMessage('Collection cannot contain elements with different families.');
        $geographyCollection->addElement(new GeometricPolygon([]));
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different SRIDs.
     */
    public function testAddElementWithInvalidSrid(): void
    {
        $geographyCollection = new GeographyCollection();
        $polygon = new GeographicPolygon([], 4326);
        $geographyCollection->addElement($polygon);
        $polygon = new GeographicPolygon([], 4327);
        $geographyCollection->addElement($polygon);

        $geographyCollection = new GeographyCollection(4326);
        $polygon = new GeographicPolygon([], 4326);
        $geographyCollection->addElement($polygon);
        static::expectException(InvalidSridException::class);
        static::expectExceptionMessage('Collection cannot contain elements with different SRIDs.');

        $polygon = new GeographicPolygon([], 4327);
        $geographyCollection->addElement($polygon);
    }

    /**
     * Test the get Elements method.
     */
    public function testGetElementsAndAddElement(): void
    {
        $geographyCollection = new GeographyCollection();
        static::assertEmpty($geographyCollection->getElements());

        $polygon = new GeographicPolygon([]);
        $geographyCollection->addElement($polygon);
        static::assertSame([$polygon], $geographyCollection->getElements());
    }

    /**
     * Test the hasElement method.
     */
    public function testHasElement(): void
    {
        $geographyCollection = new GeographyCollection();
        $polygon = new GeographicPolygon([]);
        static::assertFalse($geographyCollection->hasElement($polygon));
        $geographyCollection->addElement($polygon);
        static::assertTrue($geographyCollection->hasElement($polygon));
    }

    /**
     * Test the isEmpty method.
     */
    public function testIsEmpty(): void
    {
        $geographyCollection = new GeographyCollection();
        static::assertTrue($geographyCollection->isEmpty());
        $polygon = new GeographicPolygon([]);
        $geographyCollection->addElement($polygon);
        static::assertFalse($geographyCollection->isEmpty());
        $geographyCollection->removeElement($polygon);
        static::assertTrue($geographyCollection->isEmpty());
    }

    /**
     * Test the removeElement method.
     */
    public function testRemoveElement(): void
    {
        $geographyCollection = new GeographyCollection();
        $polygon = new GeographicPolygon([]);
        $geographyCollection->addElement($polygon);
        static::assertTrue($geographyCollection->hasElement($polygon));
        $geographyCollection->removeElement($polygon);
        static::assertFalse($geographyCollection->hasElement($polygon));
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        $geographyCollection = new GeographyCollection();
        static::assertSame([], $geographyCollection->toArray());
        $polygon = new GeographicPolygon([]);
        $geographyCollection->addElement($polygon);
        static::assertSame([[]], $geographyCollection->toArray());
        $geographyCollection->addElement(new Point(1, 2, 4326));
        static::assertSame([[], [1, 2]], $geographyCollection->toArray());
    }
}
