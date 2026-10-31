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

        try {
            (new \ReflectionMethod($aggregate, 'setElements'))->invoke($aggregate, [[1, 2]]);
        } finally {
        }
    }

    /** Custom mutable aggregate objects cannot conceal immutable descendants. */
    public function testCustomImmutableDescendant(): void
    {
        $immutable = new PointImmutable(1, 2, 4326);
        $line = static::createStub(MutableLineStringInterface::class);
        $line->method('getElements')->willReturn([$immutable]);
        $aggregate = new SpatialCollection([]);
        $this->expectException(InvalidValueException::class);

        try {
            $aggregate->addElement($line);
        } finally {
            static::assertSame([], $aggregate->getElements());
            static::assertSame([1, 2], $immutable->toArray());
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
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([[1, null]], $aggregate->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([7, 8], $replacement->toArray());
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
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([[1, null]], $aggregate->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** An immutable member is rejected by the typed insertion API. */
    public function testImmutableTypedInsertion(): void
    {
        $point = new Point(1, null, 4326);
        $aggregate = new SpatialCollection([$point]);
        $immutable = new PointImmutable(7, 8, 4326);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($aggregate, 'insertElement'))->invoke($aggregate, 1, $immutable);
        } finally {
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([7, 8], $immutable->toArray());
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
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([[1, null]], $aggregate->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([7, 8], $replacement->toArray());
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
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([[1, null]], $aggregate->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([7, 8], $replacement->toArray());
        }
    }

    /** A late wrong-layout tuple never replaces existing shared membership. */
    public function testMultiLineStringCoordinateFailure(): void
    {
        $member = new LineString([[1, null]], 4326);
        $aggregate = new MultiLineString([$member], 2154);
        $this->expectException(InvalidDimensionException::class);

        try {
            (new \ReflectionMethod($aggregate, 'setElements'))->invoke($aggregate, [[[7, 8]], [[1, 2, 3]]]);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame(4326, $member->getSrid());
            static::assertSame(2154, $aggregate->getSrid());
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
            (new \ReflectionMethod($aggregate, 'setElements'))->invoke($aggregate, [$member, $immutable]);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame([$member], $owner->getElements());
            static::assertSame(4326, $member->getSrid());
            static::assertSame(4326, $immutable->getSrid());
            static::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** A late wrong-layout tuple never replaces existing shared membership. */
    public function testMultiPointCoordinateFailure(): void
    {
        $member = new Point(1, null, 4326);
        $aggregate = new MultiPoint([$member], 2154);
        $this->expectException(InvalidDimensionException::class);

        try {
            (new \ReflectionMethod($aggregate, 'setElements'))->invoke($aggregate, [[7, 8], [1, 2, 3]]);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame(4326, $member->getSrid());
            static::assertSame(2154, $aggregate->getSrid());
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
            (new \ReflectionMethod($aggregate, 'setElements'))->invoke($aggregate, [$member, $immutable]);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame([$member], $owner->getElements());
            static::assertSame(4326, $member->getSrid());
            static::assertSame(4326, $immutable->getSrid());
            static::assertSame(2154, $aggregate->getSrid());
        }
    }

    /** A late wrong-layout tuple never replaces existing shared membership. */
    public function testMultiPolygonCoordinateFailure(): void
    {
        $member = new Polygon([[[1, null]]], 4326);
        $aggregate = new MultiPolygon([$member], 2154);
        $this->expectException(InvalidDimensionException::class);

        try {
            (new \ReflectionMethod($aggregate, 'setElements'))->invoke($aggregate, [[[[7, 8]]], [[[1, 2, 3]]]]);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame(4326, $member->getSrid());
            static::assertSame(2154, $aggregate->getSrid());
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
            (new \ReflectionMethod($aggregate, 'setElements'))->invoke($aggregate, [$member, $immutable]);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame([$member], $owner->getElements());
            static::assertSame(4326, $member->getSrid());
            static::assertSame(4326, $immutable->getSrid());
            static::assertSame(2154, $aggregate->getSrid());
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
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([[1, null]], $aggregate->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([7, 8], $replacement->toArray());
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
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([[1, null]], $aggregate->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([7, 8], $replacement->toArray());
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
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([[1, null]], $aggregate->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([7, 8], $replacement->toArray());
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
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([[1, null]], $aggregate->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame([7, 8], $replacement->toArray());
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
            (new \ReflectionMethod($aggregate, 'setElements'))->invoke($aggregate, [$member, $immutable]);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame([$member], $owner->getElements());
            static::assertSame(4326, $member->getSrid());
            static::assertSame(4326, $immutable->getSrid());
            static::assertSame(2154, $aggregate->getSrid());
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
            (new \ReflectionMethod($aggregate, 'addElement'))->invoke($aggregate, $line);
        } finally {
            static::assertSame([$point], $aggregate->getElements());
            static::assertSame([$point], $line->getPoints());
        }
    }
}
