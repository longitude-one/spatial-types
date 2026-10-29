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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension2;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\BadMethodCallException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use PHPUnit\Framework\TestCase;

/**
 * Mutable XY point contracts.
 *
 * @internal
 *
 * @coversNothing
 */
class PointTest extends TestCase
{
    /** Edits and declared context retain identity. */
    public function testEditsAndDeclaredContextRetainIdentity(): void
    {
        $point = new Point(1, 2, 4326);
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $snapshot = $point->getCoordinates();
        $reference = SpatialReference::epsg(2154);
        static::assertSame($point, $point->setX(3.5)->setY(4)->setFamily(SpatialModelEnum::GEOGRAPHY)->setSpatialReference($reference));
        static::assertSame([3.5, 4], $point->toArray());
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(GeometryTypeEnum::POINT, $point->getType());
        static::assertSame([1, 2], $snapshot?->toArray());
        static::assertSame($point, $point->setSrid(3857)->setFamily(SpatialModelEnum::GEOMETRY));
        static::assertSame(3857, $point->getSrid());
        static::assertSame([3.5, 4], $point->toArray());
    }

    /** Empty construction and recovery. */
    public function testEmptyConstructionAndRecovery(): void
    {
        $point = new Point();
        static::assertTrue($point->isEmpty());
        static::assertSame([], $point->toArray());
        static::assertSame($point, $point->setY(0));
        static::assertSame([null, 0], $point->toArray());
        static::assertSame(0, $point->getLatitude());
        static::assertNull($point->getLongitude());
    }

    /** Geography construction and independent null transitions. */
    public function testGeographyConstructionAndIndependentNullTransitions(): void
    {
        $point = new Point(1, 2, SpatialReference::epsg(4326), SpatialModelEnum::GEOGRAPHY);
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame($point, $point->setX(null));
        static::assertNull($point->getX());
        static::assertFalse($point->isEmpty());
        static::assertSame($point, $point->setY(null));
        static::assertTrue($point->isEmpty());
        static::assertSame([], $point->toArray());
        static::assertNull($point->getCoordinates());
        static::assertSame($point, $point->setX(0));
        static::assertFalse($point->isEmpty());
        static::assertSame([0, null], $point->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(4326, $point->getSrid());
    }

    /** Mutable contract excludes immutable operations. */
    public function testMutableContractExcludesImmutableOperations(): void
    {
        $point = new Point();
        static::assertContains(MutablePointInterface::class, class_implements($point));
        static::assertContains(MutableSpatialInterface::class, class_implements($point));
        static::assertNotContains(ImmutableSpatialInterface::class, class_implements($point));
        static::assertFalse((new \ReflectionClass($point))->hasMethod('withCoordinates'));
        static::assertFalse((new \ReflectionClass($point))->hasMethod('withSpatialReference'));
        static::assertFalse((new \ReflectionClass($point))->hasMethod('withSrid'));
        static::assertFalse((new \ReflectionClass($point))->hasMethod('setDimension'));
    }

    /** Rejected abscissa parsing preserves receiver. */
    public function testRejectedAbscissaParsingPreservesReceiver(): void
    {
        $point = new Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);

        try {
            $point->setX('not a coordinate');
            static::fail('Invalid coordinate must be rejected.');
        } catch (InvalidValueException) {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Rejected ordinate parsing preserves receiver. */
    public function testRejectedOrdinateParsingPreservesReceiver(): void
    {
        $point = new Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);

        try {
            $point->setY('not a coordinate');
            static::fail('Invalid coordinate must be rejected.');
        } catch (InvalidValueException) {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Setters use constructor coordinate parsing. */
    public function testSettersUseConstructorCoordinateParsing(): void
    {
        $point = new Point(1, 2);
        $constructed = new Point("2°30'E", "48°30'N");
        static::assertSame($point, $point->setX("2°30'E")->setY("48°30'N"));
        static::assertSame($constructed->getX(), $point->getX());
        static::assertSame($constructed->getY(), $point->getY());
        static::assertSame(2.5, $point->getLongitude());
        static::assertSame(48.5, $point->getLatitude());
    }

    /** Technical input rejection preserves receiver. */
    public function testTechnicalInputRejectionPreservesReceiver(): void
    {
        $point = new Point(1, 2);

        try {
            (new \ReflectionMethod($point, 'setX'))->invoke($point, []);
            static::fail('Arrays are not coordinate input.');
        } catch (\TypeError) {
            static::assertSame([1, 2], $point->toArray());
        }
    }

    /** Unavailable elevation rejects null without mutation. */
    public function testUnavailableElevationRejectsNullWithoutMutation(): void
    {
        $point = new Point(1, 2);

        try {
            $point->setZ(null);
            static::fail('An unavailable axis must be rejected.');
        } catch (BadMethodCallException) {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        }
    }

    /** Unavailable elevation rejects number without mutation. */
    public function testUnavailableElevationRejectsNumberWithoutMutation(): void
    {
        $point = new Point(1, 2);

        try {
            $point->setZ(9);
            static::fail('An unavailable axis must be rejected.');
        } catch (BadMethodCallException) {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        }
    }

    /** Unavailable measure rejects null without mutation. */
    public function testUnavailableMeasureRejectsNullWithoutMutation(): void
    {
        $point = new Point(1, 2);

        try {
            $point->setM(null);
            static::fail('An unavailable axis must be rejected.');
        } catch (BadMethodCallException) {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        }
    }

    /** Unavailable measure rejects number without mutation. */
    public function testUnavailableMeasureRejectsNumberWithoutMutation(): void
    {
        $point = new Point(1, 2);

        try {
            $point->setM(9);
            static::fail('An unavailable axis must be rejected.');
        } catch (BadMethodCallException) {
            static::assertSame([1, 2], $point->toArray());
            static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        }
    }
}
