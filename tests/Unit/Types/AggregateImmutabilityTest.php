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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractCollectionImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiLineStringImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiPolygonImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPolygonImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\Collection\AbstractLineStringCollectionImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\Collection\AbstractPointCollectionImmutable
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
        $lineString = new LineStringImmutable([[0, 0], [1, 1]]);
        $ring = new LineStringImmutable([[0, 0], [1, 0], [0, 1], [0, 0]]);
        $polygon = new PolygonImmutable([$ring]);

        yield 'line string' => [$lineString];

        yield 'multi-point' => [new MultiPointImmutable([[0, 0], [1, 1]])];

        yield 'polygon' => [$polygon];

        yield 'multi-line string' => [new MultiLineStringImmutable([$lineString])];

        yield 'multi-polygon' => [new MultiPolygonImmutable([$polygon])];

        yield 'collection' => [new GeometryCollectionImmutable(0, [$lineString, $polygon])];
    }

    /**
     * Verify that a collection receives all members at construction.
     */
    public function testCollectionReceivesItsElementsAtConstruction(): void
    {
        $point = new PointImmutable(1, 2, 2154);
        $collection = new GeometryCollectionImmutable(2154, [$point]);

        static::assertSame([$point], $collection->getElements());
        static::assertSame([[1, 2]], $collection->toArray());
    }
}
