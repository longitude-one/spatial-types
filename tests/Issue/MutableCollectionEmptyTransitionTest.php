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

        $collection->addElement($empty);
        self::assertSame([$empty], $collection->getElements());
        $collection->insertElement(1, $populated);
        self::assertSame([$empty, $populated], $collection->getElements());
        $collection->setElement(1, $empty);
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->addElement($populated);
        $collection->removeElement(2);
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->setElements([$populated]);
        self::assertSame([$populated], $collection->getElements());
        $collection->setElements([]);
        self::assertSame([], $collection->getElements());
    }

    /** Membership transitions retain EMPTY members and reflect each edit immediately. */
    public function testMultiPointMembershipTransitions(): void
    {
        $empty = new Point(null, null);
        $populated = new Point(1, 2);
        $collection = new MultiPoint([]);

        $collection->addElement($empty);
        self::assertSame([$empty], $collection->getElements());
        $collection->insertElement(1, $populated);
        self::assertSame([$empty, $populated], $collection->getElements());
        $collection->setElement(1, $empty);
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->addElement($populated);
        $collection->removeElement(2);
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->setElements([$populated]);
        self::assertSame([$populated], $collection->getElements());
        $collection->setElements([]);
        self::assertSame([], $collection->getElements());
    }

    /** Membership transitions retain EMPTY members and reflect each edit immediately. */
    public function testMultiPolygonMembershipTransitions(): void
    {
        $empty = new Polygon([]);
        $populated = new Polygon([[[0, 0], [2, 0], [0, 2], [0, 0]]]);
        $collection = new MultiPolygon([]);

        $collection->addElement($empty);
        self::assertSame([$empty], $collection->getElements());
        $collection->insertElement(1, $populated);
        self::assertSame([$empty, $populated], $collection->getElements());
        $collection->setElement(1, $empty);
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->addElement($populated);
        $collection->removeElement(2);
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->setElements([$populated]);
        self::assertSame([$populated], $collection->getElements());
        $collection->setElements([]);
        self::assertSame([], $collection->getElements());
    }

    /** Incompatible declared contexts remain representable during classification. */
    public function testMutableMixedContextMembers(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(null, null, null, 3857, SpatialModelEnum::GEOGRAPHY);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([], 2154);
        $collection = new SpatialCollection([$point, $line], 4326);

        $point->setZ(1);
        self::assertSame([null, null, 1], $point->toArray());
        $point->setZ(null);
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

        $point->setX(1);
        self::assertSame([[1, null], [[[1, null], [1, null]]]], $right->toArray());
        $point->setX(null);
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

        $child->setArrayOfCoordinates([[0, 0], [1, 1]]);
        self::assertSame([[0, 0], [1, 1]], $child->toArray());
        $child->setArrayOfCoordinates([]);
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

        $child->setArrayOfCoordinates([[[0, 0], [2, 0], [0, 2], [0, 0]]]);
        self::assertSame([[[0, 0], [2, 0], [0, 2], [0, 0]]], $child->toArray());
        $child->setArrayOfCoordinates([]);
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

        $collection->addElement($empty);
        self::assertSame([$empty], $collection->getElements());
        $collection->insertElement(1, $populated);
        self::assertSame([$empty, $populated], $collection->getElements());
        $collection->setElement(1, $empty);
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->addElement($populated);
        $collection->removeElement(2);
        self::assertSame([$empty, $empty], $collection->getElements());
        $collection->setElements([$populated]);
        self::assertSame([$populated], $collection->getElements());
        $collection->setElements([]);
        self::assertSame([], $collection->getElements());
    }
}
