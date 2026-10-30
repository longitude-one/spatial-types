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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection;
use PHPUnit\Framework\TestCase;

/** Mutable membership and shared-descendant EMPTY transitions from Story #65.
 * @internal
 *
 * @coversNothing
 */
final class MutableCollectionEmptyTransitionTest extends TestCase
{
    /** Membership transitions retain EMPTY members and reflect each edit immediately. */
    public function testMultiLineStringMembershipTransitions(): void
    {
        $empty = new LineString([]);
        $populated = new LineString([[0, 0], [1, 1]]);
        $collection = new MultiLineString([]);

        self::assertTrue($collection->isEmpty());
        $collection->addElement($empty);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty], $collection->getElements());
        $collection->insertElement(1, $populated);
        self::assertFalse($collection->isEmpty());
        self::assertSame([$empty, $populated], $collection->getElements());
        $collection->setElement(1, $empty);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->addElement($populated);
        self::assertFalse($collection->isEmpty());
        $collection->removeElement(2);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->setElements([$populated]);
        self::assertFalse($collection->isEmpty());
        self::assertSame([$populated], $collection->getElements());
        $collection->setElements([]);
        self::assertTrue($collection->isEmpty());
        self::assertSame([], $collection->getElements());
        self::assertTrue($empty->isEmpty());
        self::assertFalse($populated->isEmpty());
    }

    /** Membership transitions retain EMPTY members and reflect each edit immediately. */
    public function testMultiPointMembershipTransitions(): void
    {
        $empty = new Point(null, null);
        $populated = new Point(1, 2);
        $collection = new MultiPoint([]);

        self::assertTrue($collection->isEmpty());
        $collection->addElement($empty);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty], $collection->getElements());
        $collection->insertElement(1, $populated);
        self::assertFalse($collection->isEmpty());
        self::assertSame([$empty, $populated], $collection->getElements());
        $collection->setElement(1, $empty);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->addElement($populated);
        self::assertFalse($collection->isEmpty());
        $collection->removeElement(2);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->setElements([$populated]);
        self::assertFalse($collection->isEmpty());
        self::assertSame([$populated], $collection->getElements());
        $collection->setElements([]);
        self::assertTrue($collection->isEmpty());
        self::assertSame([], $collection->getElements());
        self::assertTrue($empty->isEmpty());
        self::assertFalse($populated->isEmpty());
    }

    /** Membership transitions retain EMPTY members and reflect each edit immediately. */
    public function testMultiPolygonMembershipTransitions(): void
    {
        $empty = new Polygon([]);
        $populated = new Polygon([[[0, 0], [2, 0], [0, 2], [0, 0]]]);
        $collection = new MultiPolygon([]);

        self::assertTrue($collection->isEmpty());
        $collection->addElement($empty);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty], $collection->getElements());
        $collection->insertElement(1, $populated);
        self::assertFalse($collection->isEmpty());
        self::assertSame([$empty, $populated], $collection->getElements());
        $collection->setElement(1, $empty);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->addElement($populated);
        self::assertFalse($collection->isEmpty());
        $collection->removeElement(2);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->setElements([$populated]);
        self::assertFalse($collection->isEmpty());
        self::assertSame([$populated], $collection->getElements());
        $collection->setElements([]);
        self::assertTrue($collection->isEmpty());
        self::assertSame([], $collection->getElements());
        self::assertTrue($empty->isEmpty());
        self::assertFalse($populated->isEmpty());
    }

    /** Incompatible declared contexts remain representable during classification. */
    public function testMutableMixedContextMembers(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(null, null, null, 3857, SpatialModelEnum::GEOGRAPHY);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([], 2154);
        $collection = new SpatialCollection([$point, $line], 4326);

        self::assertTrue($collection->isEmpty());
        $point->setZ(1);
        self::assertFalse($collection->isEmpty());
        self::assertSame([null, null, 1], $point->toArray());
        $point->setZ(null);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$point, $line], $collection->getElements());
        self::assertSame(3857, $point->getSrid());
        self::assertSame(2154, $line->getSrid());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
    }

    /** Nested collections observe edits to a shared EMPTY point without reinsertion. */
    public function testNestedSharedPointTransitions(): void
    {
        $point = new Point(null, null, 3857);
        $members = new MultiPoint([$point, $point], 4326);
        $left = new SpatialCollection([$members]);
        $right = new SpatialCollection([$point, $left]);

        self::assertTrue($members->isEmpty());
        self::assertTrue($left->isEmpty());
        self::assertTrue($right->isEmpty());
        $point->setX(1);
        self::assertFalse($members->isEmpty());
        self::assertFalse($left->isEmpty());
        self::assertFalse($right->isEmpty());
        self::assertSame([[1, null], [[[1, null], [1, null]]]], $right->toArray());
        $point->setX(null);
        self::assertTrue($members->isEmpty());
        self::assertTrue($left->isEmpty());
        self::assertTrue($right->isEmpty());
        self::assertSame([[], [[[], []]]], $right->toArray());
        self::assertSame([$point, $point], $members->getElements());
        self::assertSame([$point, $left], $right->getElements());
        self::assertSame(3857, $point->getSrid());
        self::assertSame(4326, $members->getSrid());
    }

    /** Shared child edits propagate through both holders and a nested collection. */
    public function testSharedLineStringTransitions(): void
    {
        $child = new LineString([], 4326);
        $left = new MultiLineString([$child], 4326);
        $right = new MultiLineString([$child], 4326);
        $root = new SpatialCollection([$left, $right], 4326);

        self::assertTrue($root->isEmpty());
        $child->setArrayOfCoordinates([[0, 0], [1, 1]]);
        self::assertFalse($left->isEmpty());
        self::assertFalse($right->isEmpty());
        self::assertFalse($root->isEmpty());
        self::assertSame([[0, 0], [1, 1]], $child->toArray());
        $child->setArrayOfCoordinates([]);
        self::assertTrue($left->isEmpty());
        self::assertTrue($right->isEmpty());
        self::assertTrue($root->isEmpty());
        self::assertSame([$child], $left->getElements());
        self::assertSame([$child], $right->getElements());
        self::assertSame([[[]], [[]]], $root->toArray());
        self::assertSame(4326, $child->getSrid());
    }

    /** Shared child edits propagate through both holders and a nested collection. */
    public function testSharedPolygonTransitions(): void
    {
        $child = new Polygon([], 4326);
        $left = new MultiPolygon([$child], 4326);
        $right = new MultiPolygon([$child], 4326);
        $root = new SpatialCollection([$left, $right], 4326);

        self::assertTrue($root->isEmpty());
        $child->setArrayOfCoordinates([[[0, 0], [2, 0], [0, 2], [0, 0]]]);
        self::assertFalse($left->isEmpty());
        self::assertFalse($right->isEmpty());
        self::assertFalse($root->isEmpty());
        self::assertSame([[[0, 0], [2, 0], [0, 2], [0, 0]]], $child->toArray());
        $child->setArrayOfCoordinates([]);
        self::assertTrue($left->isEmpty());
        self::assertTrue($right->isEmpty());
        self::assertTrue($root->isEmpty());
        self::assertSame([$child], $left->getElements());
        self::assertSame([$child], $right->getElements());
        self::assertSame([[[]], [[]]], $root->toArray());
        self::assertSame(4326, $child->getSrid());
    }

    /** Membership transitions retain EMPTY members and reflect each edit immediately. */
    public function testSpatialCollectionMembershipTransitions(): void
    {
        $empty = new Point(null, null);
        $populated = new Point(1, 2);
        $collection = new SpatialCollection([]);

        self::assertTrue($collection->isEmpty());
        $collection->addElement($empty);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty], $collection->getElements());
        $collection->insertElement(1, $populated);
        self::assertFalse($collection->isEmpty());
        self::assertSame([$empty, $populated], $collection->getElements());
        $collection->setElement(1, $empty);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->addElement($populated);
        self::assertFalse($collection->isEmpty());
        $collection->removeElement(2);
        self::assertTrue($collection->isEmpty());
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->setElements([$populated]);
        self::assertFalse($collection->isEmpty());
        self::assertSame([$populated], $collection->getElements());
        $collection->setElements([]);
        self::assertTrue($collection->isEmpty());
        self::assertSame([], $collection->getElements());
        self::assertTrue($empty->isEmpty());
        self::assertFalse($populated->isEmpty());
    }
}
