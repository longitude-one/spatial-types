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

use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection;
use PHPUnit\Framework\TestCase;

/** Mutable aggregate contracts.
 * @internal
 *
 * @coversNothing
 */
class MutableMultiObjectTest extends TestCase
{
    /** Every membership edit keeps identity, order and zero-based addressing. */
    public function testMultiLineStringMembership(): void
    {
        $first = new LineString([[1, null]], 4326);
        $second = new LineString([[7, 8]]);
        $aggregate = new MultiLineString([7 => $first]);
        static::assertSame($aggregate, $aggregate->addElement($first)->insertElement(1, $second));
        static::assertSame([$first, $second, $first], $aggregate->getLineStrings());
        static::assertSame([[[1, null]], [[7, 8]], [[1, null]]], $aggregate->toArray());
        static::assertSame($first, $aggregate->getElement(0));
        static::assertSame($aggregate, $aggregate->insertElement(3, $second)->setElement(0, $second)->removeElement(1));
        static::assertSame([$second, $first, $second], $aggregate->getElements());
        static::assertSame($aggregate, $aggregate->setElements([9 => $first, 4 => $second]));
        static::assertSame([$first, $second], $aggregate->getElements());
        $returned = $aggregate->getElements();
        $returned[0] = $second;
        static::assertSame($first, $aggregate->getElement(0));
        static::assertSame(4326, $first->getSrid());
        static::assertSame($aggregate, $aggregate->setElements([]));
        static::assertSame([], $aggregate->toArray());
    }

    /** Every membership edit keeps identity, order and zero-based addressing. */
    public function testMultiPointMembership(): void
    {
        $first = new Point(1, null, 4326);
        $second = new Point(7, 8);
        $aggregate = new MultiPoint([7 => $first]);
        static::assertSame($aggregate, $aggregate->addElement($first)->insertElement(1, $second));
        static::assertSame([$first, $second, $first], $aggregate->getPoints());
        static::assertSame([[1, null], [7, 8], [1, null]], $aggregate->toArray());
        static::assertSame($first, $aggregate->getElement(0));
        static::assertSame($aggregate, $aggregate->insertElement(3, $second)->setElement(0, $second)->removeElement(1));
        static::assertSame([$second, $first, $second], $aggregate->getElements());
        static::assertSame($aggregate, $aggregate->setElements([9 => $first, 4 => $second]));
        static::assertSame([$first, $second], $aggregate->getElements());
        $returned = $aggregate->getElements();
        $returned[0] = $second;
        static::assertSame($first, $aggregate->getElement(0));
        static::assertSame(4326, $first->getSrid());
        static::assertSame($aggregate, $aggregate->setElements([]));
        static::assertSame([], $aggregate->toArray());
    }

    /** Every membership edit keeps identity, order and zero-based addressing. */
    public function testMultiPolygonMembership(): void
    {
        $first = new Polygon([[[1, null]]], 4326);
        $second = new Polygon([[[7, 8]]]);
        $aggregate = new MultiPolygon([7 => $first]);
        static::assertSame($aggregate, $aggregate->addElement($first)->insertElement(1, $second));
        static::assertSame([$first, $second, $first], $aggregate->getPolygons());
        static::assertSame([[[[1, null]]], [[[7, 8]]], [[[1, null]]]], $aggregate->toArray());
        static::assertSame($first, $aggregate->getElement(0));
        static::assertSame($aggregate, $aggregate->insertElement(3, $second)->setElement(0, $second)->removeElement(1));
        static::assertSame([$second, $first, $second], $aggregate->getElements());
        static::assertSame($aggregate, $aggregate->setElements([9 => $first, 4 => $second]));
        static::assertSame([$first, $second], $aggregate->getElements());
        $returned = $aggregate->getElements();
        $returned[0] = $second;
        static::assertSame($first, $aggregate->getElement(0));
        static::assertSame(4326, $first->getSrid());
        static::assertSame($aggregate, $aggregate->setElements([]));
        static::assertSame([], $aggregate->toArray());
    }

    /** A shared descendant stays observable through different aggregate kinds. */
    public function testSharedDescendants(): void
    {
        $point = new Point(1, null, 4326);
        $line = new LineString([$point], 4326);
        $polygon = new Polygon([$line], 4326);
        $points = new MultiPoint([$point]);
        $lines = new MultiLineString([$line]);
        $polygons = new MultiPolygon([$polygon]);
        $nested = new SpatialCollection([$points, $lines, $polygons]);
        $owner = new SpatialCollection([$nested, $polygon]);
        $point->setX(9);
        static::assertSame([[9, null]], $points->toArray());
        static::assertSame([[[9, null]]], $lines->toArray());
        static::assertSame([[[[9, null]]]], $polygons->toArray());
        static::assertSame($nested, $owner->getElement(0));
        static::assertSame($polygon, $polygons->getElement(0));
        static::assertSame($line, $polygon->getRing(0));
        static::assertSame($point, $points->getElement(0));
        $replacement = new Point(7, 8);
        $points->setElement(0, $replacement);
        static::assertSame([[7, 8]], $points->toArray());
        static::assertSame([[[9, null]]], $lines->toArray());
    }

    /** Duplicate member positions are retained and can be inspected explicitly. */
    public function testSimplicity(): void
    {
        $point = new Point(1, 2);
        $points = new MultiPoint([$point, $point]);
        static::assertSame($points, $points->setElements([new Point(1, 2), new Point(1, 2)]));
        static::assertSame($points, $points->removeElement(1));
        static::assertSame($points, $points->setElements([]));
    }

    /** Every membership edit keeps identity, order and zero-based addressing. */
    public function testSpatialCollectionMembership(): void
    {
        $first = new Point(1, null, 4326);
        $second = new Point(7, 8);
        $aggregate = new SpatialCollection([7 => $first]);
        static::assertSame($aggregate, $aggregate->addElement($first)->insertElement(1, $second));
        static::assertSame([$first, $second, $first], $aggregate->getElements());
        static::assertSame([[1, null], [7, 8], [1, null]], $aggregate->toArray());
        static::assertSame($first, $aggregate->getElement(0));
        static::assertSame($aggregate, $aggregate->insertElement(3, $second)->setElement(0, $second)->removeElement(1));
        static::assertSame([$second, $first, $second], $aggregate->getElements());
        static::assertSame($aggregate, $aggregate->setElements([9 => $first, 4 => $second]));
        static::assertSame([$first, $second], $aggregate->getElements());
        $returned = $aggregate->getElements();
        $returned[0] = $second;
        static::assertSame($first, $aggregate->getElement(0));
        static::assertSame(4326, $first->getSrid());
        static::assertSame($aggregate, $aggregate->setElements([]));
        static::assertSame([], $aggregate->toArray());
    }
}
