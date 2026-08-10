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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Trait\LineStringTrait
 * @covers \LongitudeOne\SpatialTypes\Trait\PointTrait
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractCollection
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPolygon
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolygon
 */
class AggregateImmutabilityTest extends TestCase
{
    /**
     * @param SpatialInterface $aggregate aggregate to inspect
     */
    #[DataProvider('provideAggregates')]
    public function testAggregateHasNoPublicMembershipMutator(SpatialInterface $aggregate): void
    {
        foreach ([
            'addPoint',
            'addPoints',
            'addRing',
            'addRings',
            'addLineString',
            'addLineStrings',
            'addPolygon',
            'addPolygons',
            'addElement',
            'removeElement',
        ] as $method) {
            if (method_exists($aggregate, $method)) {
                static::assertFalse((new \ReflectionMethod($aggregate, $method))->isPublic(), sprintf('%s::%s() must not be public.', $aggregate::class, $method));
            }
        }
    }

    /**
     * @return \Generator<string, array{0: SpatialInterface}, null, void>
     */
    public static function provideAggregates(): \Generator
    {
        $lineString = new LineString([[0, 0], [1, 1]]);
        $ring = new LineString([[0, 0], [1, 0], [0, 1], [0, 0]]);
        $polygon = new Polygon([$ring]);

        yield 'line string' => [$lineString];

        yield 'multi-point' => [new MultiPoint([[0, 0], [1, 1]])];

        yield 'polygon' => [$polygon];

        yield 'multi-line string' => [new MultiLineString([$lineString])];

        yield 'multi-polygon' => [new MultiPolygon([$polygon])];

        yield 'collection' => [new GeometryCollection(0, [$lineString, $polygon])];
    }

    /**
     * Verify that a collection receives all members at construction.
     */
    public function testCollectionReceivesItsElementsAtConstruction(): void
    {
        $point = new Point(1, 2, 2154);
        $collection = new GeometryCollection(2154, [$point]);

        static::assertSame([$point], $collection->getElements());
        static::assertSame([[1, 2]], $collection->toArray());
    }
}
