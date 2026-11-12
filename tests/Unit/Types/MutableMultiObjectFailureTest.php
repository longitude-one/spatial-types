<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
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
class MutableMultiObjectFailureTest extends TestCase
{
    /** Heterogeneous coordinate arrays carry no type information and are rejected. */
    public function testCollectionCoordinateArrayRejection(): void
    {
        $aggregate = new SpatialCollection([]);
        $this->expectException(InvalidValueException::class);

        new \ReflectionMethod($aggregate, 'setElements')->invoke($aggregate, [[1, 2]]);
    }

    /** Custom mutable aggregate objects cannot conceal immutable descendants. */
    public function testCustomImmutableDescendant(): void
    {
        $immutable = new PointImmutable(1, 2, 4326);
        $line = self::createStub(MutableLineStringInterface::class);
        $line->method('getElements')->willReturn([$immutable]);
        $aggregate = new SpatialCollection([]);
        $this->expectException(InvalidValueException::class);

        try {
            $aggregate->addElement($line);
        } finally {
            self::assertSame([], $aggregate->getElements());
            self::assertSame([1, 2], $immutable->toArray());
        }
    }

    /** Invalid addressing preserves the aggregate and its referenced member. */
    public function testgetElementBeyondRange(): void
    {
        $point = new Point(1, null, 4326);
        $replacement = new Point(7, 8, 3857);
        $aggregate = new MultiPoint([$point], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $aggregate->getElement(1);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([[1, null]], $aggregate->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** Invalid addressing preserves the aggregate and its referenced member. */
    public function testgetElementNegative(): void
    {
        $point = new Point(1, null, 4326);
        $replacement = new Point(7, 8, 3857);
        $aggregate = new MultiPoint([$point], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $aggregate->getElement(-1);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([[1, null]], $aggregate->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** An immutable member is rejected by the typed insertion API. */
    public function testImmutableTypedInsertion(): void
    {
        $point = new Point(1, null, 4326);
        $aggregate = new SpatialCollection([$point]);
        $immutable = new PointImmutable(7, 8, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($aggregate, 'insertElement')->invoke($aggregate, 1, $immutable);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([7, 8], $immutable->toArray());
        }
    }

    /** Invalid addressing preserves the aggregate and its referenced member. */
    public function testinsertElementBeyondRange(): void
    {
        $point = new Point(1, null, 4326);
        $replacement = new Point(7, 8, 3857);
        $aggregate = new MultiPoint([$point], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $aggregate->insertElement(2, $replacement);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([[1, null]], $aggregate->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** Invalid addressing preserves the aggregate and its referenced member. */
    public function testinsertElementNegative(): void
    {
        $point = new Point(1, null, 4326);
        $replacement = new Point(7, 8, 3857);
        $aggregate = new MultiPoint([$point], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $aggregate->insertElement(-1, $replacement);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([[1, null]], $aggregate->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** A late wrong-layout tuple never replaces existing shared membership. */
    public function testMultiLineStringCoordinateFailure(): void
    {
        $member = new LineString([[1, null]], 4326);
        $aggregate = new MultiLineString([$member], 2154);
        $this->expectException(InvalidDimensionException::class);

        try {
            new \ReflectionMethod($aggregate, 'setElements')->invoke($aggregate, [[[7, 8]], [[1, 2, 3]]]);
        } finally {
            self::assertSame([$member], $aggregate->getElements());
            self::assertSame(4326, $member->getSrid());
            self::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** Late immutable membership fails atomically without altering shared members. */
    public function testMultiLineStringImmutableRejection(): void
    {
        $member = new LineString([[1, null]], 4326);
        $immutable = new LineStringImmutable([[1, 2]], 4326);
        $aggregate = new MultiLineString([$member], 2154);
        $owner = new SpatialCollection([$member]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($aggregate, 'setElements')->invoke($aggregate, [$member, $immutable]);
        } finally {
            self::assertSame([$member], $aggregate->getElements());
            self::assertSame([$member], $owner->getElements());
            self::assertSame(4326, $member->getSrid());
            self::assertSame(4326, $immutable->getSrid());
            self::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** A late wrong-layout tuple never replaces existing shared membership. */
    public function testMultiPointCoordinateFailure(): void
    {
        $member = new Point(1, null, 4326);
        $aggregate = new MultiPoint([$member], 2154);
        $this->expectException(InvalidDimensionException::class);

        try {
            new \ReflectionMethod($aggregate, 'setElements')->invoke($aggregate, [[7, 8], [1, 2, 3]]);
        } finally {
            self::assertSame([$member], $aggregate->getElements());
            self::assertSame(4326, $member->getSrid());
            self::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** Late immutable membership fails atomically without altering shared members. */
    public function testMultiPointImmutableRejection(): void
    {
        $member = new Point(1, null, 4326);
        $immutable = new PointImmutable(1, 2, 4326);
        $aggregate = new MultiPoint([$member], 2154);
        $owner = new SpatialCollection([$member]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($aggregate, 'setElements')->invoke($aggregate, [$member, $immutable]);
        } finally {
            self::assertSame([$member], $aggregate->getElements());
            self::assertSame([$member], $owner->getElements());
            self::assertSame(4326, $member->getSrid());
            self::assertSame(4326, $immutable->getSrid());
            self::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** A late wrong-layout tuple never replaces existing shared membership. */
    public function testMultiPolygonCoordinateFailure(): void
    {
        $member = new Polygon([[[1, null]]], 4326);
        $aggregate = new MultiPolygon([$member], 2154);
        $this->expectException(InvalidDimensionException::class);

        try {
            new \ReflectionMethod($aggregate, 'setElements')->invoke($aggregate, [[[[7, 8]]], [[[1, 2, 3]]]]);
        } finally {
            self::assertSame([$member], $aggregate->getElements());
            self::assertSame(4326, $member->getSrid());
            self::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** Late immutable membership fails atomically without altering shared members. */
    public function testMultiPolygonImmutableRejection(): void
    {
        $member = new Polygon([[[1, null]]], 4326);
        $immutable = new PolygonImmutable([[[1, 2]]], 4326);
        $aggregate = new MultiPolygon([$member], 2154);
        $owner = new SpatialCollection([$member]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($aggregate, 'setElements')->invoke($aggregate, [$member, $immutable]);
        } finally {
            self::assertSame([$member], $aggregate->getElements());
            self::assertSame([$member], $owner->getElements());
            self::assertSame(4326, $member->getSrid());
            self::assertSame(4326, $immutable->getSrid());
            self::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** Invalid addressing preserves the aggregate and its referenced member. */
    public function testremoveElementBeyondRange(): void
    {
        $point = new Point(1, null, 4326);
        $replacement = new Point(7, 8, 3857);
        $aggregate = new MultiPoint([$point], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $aggregate->removeElement(1);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([[1, null]], $aggregate->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** Invalid addressing preserves the aggregate and its referenced member. */
    public function testremoveElementNegative(): void
    {
        $point = new Point(1, null, 4326);
        $replacement = new Point(7, 8, 3857);
        $aggregate = new MultiPoint([$point], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $aggregate->removeElement(-1);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([[1, null]], $aggregate->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** Invalid addressing preserves the aggregate and its referenced member. */
    public function testsetElementBeyondRange(): void
    {
        $point = new Point(1, null, 4326);
        $replacement = new Point(7, 8, 3857);
        $aggregate = new MultiPoint([$point], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $aggregate->setElement(1, $replacement);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([[1, null]], $aggregate->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** Invalid addressing preserves the aggregate and its referenced member. */
    public function testsetElementNegative(): void
    {
        $point = new Point(1, null, 4326);
        $replacement = new Point(7, 8, 3857);
        $aggregate = new MultiPoint([$point], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $aggregate->setElement(-1, $replacement);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([[1, null]], $aggregate->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** Late immutable membership fails atomically without altering shared members. */
    public function testSpatialCollectionImmutableRejection(): void
    {
        $member = new Point(1, null, 4326);
        $immutable = new PointImmutable(1, 2, 4326);
        $aggregate = new SpatialCollection([$member], 2154);
        $owner = new SpatialCollection([$member]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($aggregate, 'setElements')->invoke($aggregate, [$member, $immutable]);
        } finally {
            self::assertSame([$member], $aggregate->getElements());
            self::assertSame([$member], $owner->getElements());
            self::assertSame(4326, $member->getSrid());
            self::assertSame(4326, $immutable->getSrid());
            self::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** Typed spatial input of the wrong member kind is rejected before mutation. */
    public function testWrongMutableKind(): void
    {
        $point = new Point(1, null, 4326);
        $aggregate = new MultiPoint([$point]);
        $line = new LineString([$point], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($aggregate, 'addElement')->invoke($aggregate, $line);
        } finally {
            self::assertSame([$point], $aggregate->getElements());
            self::assertSame([$point], $line->getPoints());
        }
    }
}
