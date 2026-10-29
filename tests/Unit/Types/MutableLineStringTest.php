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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use PHPUnit\Framework\TestCase;

/** Mutable linear membership and ownership.
 * @internal
 *
 * @coversNothing
 */
class MutableLineStringTest extends TestCase
{
    /** Addition rejects immutable components atomically. */
    public function testAdditionRejectsImmutableComponents(): void
    {
        $point = new Point(1, 2);
        $line = new LineString([$point]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($line, 'addPoint'))->invoke($line, new PointImmutable(3, 4));
        } finally {
            static::assertSame([$point], $line->getPoints());
        }
    }

    /** Constructor rejects immutable components without modifying them. */
    public function testConstructorRejectsImmutableComponents(): void
    {
        $point = new PointImmutable(1, 2, 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(LineString::class))->newInstance([$point]);
        } finally {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** A later parsing failure cannot replace earlier membership. */
    public function testCoordinateReplacementParsingFailureIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            $line->setArrayOfCoordinates([[3, 4], ['not a coordinate', 5]]);
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $line->getSrid());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** Empty and incomplete paths remain editable without closing or repair. */
    public function testEmptyAndIncompletePathsRemainEditable(): void
    {
        $line = new LineString([]);
        static::assertTrue($line->isEmpty());
        static::assertNull($line->getStartPoint());
        static::assertNull($line->getEndPoint());
        static::assertFalse($line->isClosed());
        static::assertFalse($line->isLine());
        $line->addPoint(new Point(0, null));
        static::assertFalse($line->isEmpty());
        static::assertFalse($line->isClosed());
        static::assertFalse($line->isLine());
        $line->addPoint(new Point(0, null));
        static::assertTrue($line->isLine());
        static::assertTrue($line->isClosed());
        $line->setPoint(1, new Point(1, 2));
        static::assertFalse($line->isClosed());
        static::assertSame([[0, null], [1, 2]], $line->toArray());
        static::assertSame($line, $line->setArrayOfCoordinates([]));
        static::assertTrue($line->isEmpty());
    }

    /** Extra coordinates are rejected without mutation. */
    public function testExtraTupleCoordinateIsRejectedAtomically(): void
    {
        $point = new Point(1, 2);
        $line = new LineString([$point]);
        $this->expectException(InvalidDimensionException::class);

        try {
            $line->setArrayOfCoordinates([[3, 4], [5, 6, 7]]);
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
        }
    }

    /** Insertion rejects immutable components atomically. */
    public function testInsertionRejectsImmutableComponents(): void
    {
        $point = new Point(1, 2);
        $line = new LineString([$point]);
        $immutable = new PointImmutable(3, 4);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($line, 'insertPoint'))->invoke($line, 0, $immutable);
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([1, 2], $point->toArray());
            static::assertSame([3, 4], $immutable->toArray());
        }
    }

    /** Membership edits preserve order and identity. */
    public function testMembershipEditsPreserveOrderAndIdentity(): void
    {
        $first = new Point(0, null);
        $middle = new Point(2, 3);
        $last = new Point(4, 5);
        $replacement = new Point(null, 6);
        $line = new LineString([]);
        static::assertSame($line, $line->addPoint($first)->insertPoint(1, $last)->insertPoint(1, $middle));
        static::assertSame([$first, $middle, $last], $line->getPoints());
        static::assertSame($first, $line->getStartPoint());
        static::assertSame($last, $line->getEndPoint());
        static::assertSame($middle, $line->getPoint(1));
        static::assertSame($line, $line->setPoint(1, $replacement)->removePoint(0));
        static::assertSame([$replacement, $last], $line->getElements());
        static::assertSame([[null, 6], [4, 5]], $line->toArray());
        static::assertSame($line, $line->setArrayOfCoordinates([[0, null], [null, null]]));
        static::assertSame([[0, null], []], $line->toArray());
        static::assertInstanceOf(Point::class, $line->getPoint(0));
        static::assertSame([0, null], $first->toArray());
        static::assertSame([null, 6], $replacement->toArray());
    }

    /** Typed contracts expose neither immutable operations nor dimension changes. */
    public function testMutableContractsExcludeCopyAndDimensionSetters(): void
    {
        $line = new LineString([]);
        static::assertContains(MutableLineStringInterface::class, class_implements($line));
        static::assertNotContains(ImmutableSpatialInterface::class, class_implements($line));
        static::assertFalse((new \ReflectionClass($line))->hasMethod('withPoint'));
        static::assertFalse((new \ReflectionClass($line))->hasMethod('withArrayOfCoordinates'));
        static::assertFalse((new \ReflectionClass($line))->hasMethod('withSrid'));
        static::assertFalse((new \ReflectionClass($line))->hasMethod('withSpatialReference'));
        static::assertFalse((new \ReflectionClass($line))->hasMethod('setDimension'));
    }

    /** NegativeAccess fails without changing membership or referenced points. */
    public function testNegativeAccessIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(OutOfBoundsException::class);

        try {
            $line->getPoint(-1);
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** NegativeInsertion fails without changing membership or referenced points. */
    public function testNegativeInsertionIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(OutOfBoundsException::class);

        try {
            $line->insertPoint(-1, new Point(3, 4));
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** NegativeRemoval fails without changing membership or referenced points. */
    public function testNegativeRemovalIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(OutOfBoundsException::class);

        try {
            $line->removePoint(-1);
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** NegativeReplacement fails without changing membership or referenced points. */
    public function testNegativeReplacementIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(OutOfBoundsException::class);

        try {
            $line->setPoint(-1, new Point(3, 4));
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** OverflowAccess fails without changing membership or referenced points. */
    public function testOverflowAccessIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(OutOfBoundsException::class);

        try {
            $line->getPoint(1);
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** OverflowInsertion fails without changing membership or referenced points. */
    public function testOverflowInsertionIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(OutOfBoundsException::class);

        try {
            $line->insertPoint(2, new Point(3, 4));
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** OverflowRemoval fails without changing membership or referenced points. */
    public function testOverflowRemovalIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(OutOfBoundsException::class);

        try {
            $line->removePoint(1);
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** OverflowReplacement fails without changing membership or referenced points. */
    public function testOverflowReplacementIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $line = new LineString([$point], 4326);
        $this->expectException(OutOfBoundsException::class);

        try {
            $line->setPoint(1, new Point(3, 4));
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** Replacement rejects immutable components atomically. */
    public function testReplacementRejectsImmutableComponents(): void
    {
        $point = new Point(1, 2);
        $line = new LineString([$point]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($line, 'setPoint'))->invoke($line, 0, new PointImmutable(3, 4));
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([1, 2], $point->toArray());
        }
    }

    /** Shared points are retained without rewriting their initial context. */
    public function testSharedPointsRetainIdentityAndInitialContext(): void
    {
        $point = new Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);
        $first = new LineString([$point], 2154);
        $second = new LineString([$point]);
        static::assertSame($point, $first->getPoint(0));
        static::assertSame($point, $second->getPoint(0));
        static::assertSame(4326, $point->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        $point->setX(null)->setY(0);
        static::assertSame([[null, 0]], $first->toArray());
        static::assertSame([[null, 0]], $second->toArray());
        $points = $first->getPoints();
        $points[] = new Point(7, 8);
        static::assertSame([$point], $first->getPoints());
    }

    /** Technical input cannot partially replace coordinate content. */
    public function testTechnicalTupleInputFailureIsAtomic(): void
    {
        $point = new Point(1, 2);
        $line = new LineString([$point]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($line, 'setArrayOfCoordinates'))->invoke($line, [[3, 4], [[], 5]]);
        } finally {
            static::assertSame([$point], $line->getPoints());
            static::assertSame([[1, 2]], $line->toArray());
        }
    }

    /** Tuple parsing uses the mutable point constructor. */
    public function testTupleParsingUsesPointConstructor(): void
    {
        $line = new LineString([["2°30'E", "48°30'N"]], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[2.5, 48.5]], $line->toArray());
        static::assertSame(4326, $line->getPoint(0)->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getPoint(0)->getFamily());
        static::assertSame(GeometryTypeEnum::LINESTRING, $line->getType());
    }
}
