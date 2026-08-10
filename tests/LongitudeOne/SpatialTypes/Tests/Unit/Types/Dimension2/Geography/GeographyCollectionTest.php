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

use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon as GeometricPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point as GeographicPoint3D;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractCollection
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollection
 */
class GeographyCollectionTest extends TestCase
{
    /**
     * Test that the addElement method throws an exception when developers try to add another GeographyCollection.
     */
    public function testAddElementWithGeographyCollection(): void
    {
        static::expectException(InvalidValueException::class);
        static::expectExceptionMessageIsOrContains('An instance of LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollection cannot contain another GeometryCollection nor GeographyCollection.');
        new GeographyCollection(0, [new GeographyCollection()]);
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different dimensions.
     */
    public function testAddElementWithInvalidDimension(): void
    {
        static::expectException(InvalidDimensionException::class);
        static::expectExceptionMessageIsOrContains('Collection cannot contain elements with different dimensions.');
        new GeographyCollection(0, [new GeographicPoint3D(0, 1, 2)]);
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different families.
     */
    public function testAddElementWithInvalidFamily(): void
    {
        static::expectException(InvalidFamilyException::class);
        static::expectExceptionMessageIsOrContains('Collection cannot contain elements with different families.');
        new GeographyCollection(0, [new GeometricPolygon([])]);
    }

    /**
     * Test that the addElement method throws an exception when developers try to add an element with different SRIDs.
     */
    public function testAddElementWithInvalidSrid(): void
    {
        $polygon = new GeographicPolygon([], 4326);
        $polygon = new GeographicPolygon([], 4327);
        new GeographyCollection(0, [new GeographicPolygon([], 4326), $polygon]);

        $polygon = new GeographicPolygon([], 4326);
        static::expectException(InvalidSridException::class);
        static::expectExceptionMessageIsOrContains('Collection cannot contain elements with different SRIDs.');

        $polygon = new GeographicPolygon([], 4327);
        new GeographyCollection(4326, [new GeographicPolygon([], 4326), $polygon]);
    }

    /**
     * Test the get Elements method.
     */
    public function testGetElements(): void
    {
        $polygon = new GeographicPolygon([]);
        $geographyCollection = new GeographyCollection(0, [$polygon]);
        static::assertSame([$polygon], $geographyCollection->getElements());
    }

    /**
     * Test the type getter.
     */
    public function testGetType(): void
    {
        static::assertSame(TypeEnum::COLLECTION, (new GeographyCollection())->getType());
    }

    /**
     * Test the hasElement method.
     */
    public function testHasElement(): void
    {
        $polygon = new GeographicPolygon([]);
        $geographyCollection = new GeographyCollection(0, [$polygon]);
        static::assertTrue($geographyCollection->hasElement($polygon));
    }

    /**
     * Test the isEmpty method.
     */
    public function testIsEmpty(): void
    {
        $polygon = new GeographicPolygon([]);
        static::assertTrue((new GeographyCollection())->isEmpty());
        $geographyCollection = new GeographyCollection(0, [$polygon]);
        static::assertFalse($geographyCollection->isEmpty());
    }

    /**
     * Test the toArray method.
     */
    public function testToArray(): void
    {
        $polygon = new GeographicPolygon([]);
        static::assertSame([], (new GeographyCollection())->toArray());
        $geographyCollection = new GeographyCollection(0, [$polygon, new Point(1, 2, 4326)]);
        static::assertSame([[], [1, 2]], $geographyCollection->toArray());
    }
}
