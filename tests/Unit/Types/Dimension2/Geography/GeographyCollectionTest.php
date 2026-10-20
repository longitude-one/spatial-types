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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Polygon as GeographicPolygon;
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
     * Test that construction accepts another GeographyCollection.
     *
     * @see https://github.com/longitude-one/spatial-types/issues/1
     */
    public function testAddElementWithGeographyCollection(): void
    {
        $nestedCollection = new GeographyCollection();
        $geographyCollection = new GeographyCollection(0, [$nestedCollection]);

        static::assertSame([$nestedCollection], $geographyCollection->getElements());
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
        static::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, (new GeographyCollection())->getType());
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
        $polygon = new GeographicPolygon([], 4326);
        static::assertSame([], (new GeographyCollection())->toArray());
        $geographyCollection = new GeographyCollection(4326, [$polygon, new Point(1, 2, 4326)]);
        static::assertSame([[], [1, 2]], $geographyCollection->toArray());
    }
}
