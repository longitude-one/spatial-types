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
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        $snapshot = $point->getCoordinates();
        $reference = SpatialReference::epsg(2154);
        self::assertSame($point, $point->setX(3.5)->setY(4)->setFamily(SpatialModelEnum::GEOGRAPHY)->setSpatialReference($reference));
        self::assertSame([3.5, 4], $point->toArray());
        self::assertSame($reference, $point->getSpatialReference());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(GeometryTypeEnum::POINT, $point->getType());
        self::assertSame([1, 2], $snapshot?->toArray());
        self::assertSame($point, $point->setSrid(3857)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame(3857, $point->getSrid());
        self::assertSame([3.5, 4], $point->toArray());
    }

    /** Empty construction and recovery. */
    public function testEmptyConstructionAndRecovery(): void
    {
        $point = new Point();
        self::assertSame([], $point->toArray());
        self::assertSame($point, $point->setY(0));
        self::assertSame([null, 0], $point->toArray());
        self::assertSame(0, $point->getLatitude());
        self::assertNull($point->getLongitude());
    }

    /** Geography construction and independent null transitions. */
    public function testGeographyConstructionAndIndependentNullTransitions(): void
    {
        $point = new Point(1, 2, SpatialReference::epsg(4326), SpatialModelEnum::GEOGRAPHY);
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame($point, $point->setX(null));
        self::assertNull($point->getX());
        self::assertSame($point, $point->setY(null));
        self::assertSame([], $point->toArray());
        self::assertNull($point->getCoordinates());
        self::assertSame($point, $point->setX(0));
        self::assertSame([0, null], $point->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(4326, $point->getSrid());
    }

    /** Mutable contract excludes immutable operations. */
    public function testMutableContractExcludesImmutableOperations(): void
    {
        $point = new Point();
        self::assertContains(MutablePointInterface::class, class_implements($point));
        self::assertContains(MutableSpatialInterface::class, class_implements($point));
        self::assertNotContains(ImmutableSpatialInterface::class, class_implements($point));
        self::assertFalse(new \ReflectionClass($point)->hasMethod('withCoordinates'));
        self::assertFalse(new \ReflectionClass($point)->hasMethod('withSpatialReference'));
        self::assertFalse(new \ReflectionClass($point)->hasMethod('withSrid'));
        self::assertFalse(new \ReflectionClass($point)->hasMethod('setDimension'));
    }

    /** Rejected abscissa parsing preserves receiver. */
    public function testRejectedAbscissaParsingPreservesReceiver(): void
    {
        $point = new Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);

        try {
            $point->setX('not a coordinate');
            self::fail('Invalid coordinate must be rejected.');
        } catch (InvalidValueException) {
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Rejected ordinate parsing preserves receiver. */
    public function testRejectedOrdinateParsingPreservesReceiver(): void
    {
        $point = new Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);

        try {
            $point->setY('not a coordinate');
            self::fail('Invalid coordinate must be rejected.');
        } catch (InvalidValueException) {
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        }
    }

    /** Setters use constructor coordinate parsing. */
    public function testSettersUseConstructorCoordinateParsing(): void
    {
        $point = new Point(1, 2);
        $constructed = new Point("2°30'E", "48°30'N");
        self::assertSame($point, $point->setX("2°30'E")->setY("48°30'N"));
        self::assertSame($constructed->getX(), $point->getX());
        self::assertSame($constructed->getY(), $point->getY());
        self::assertSame(2.5, $point->getLongitude());
        self::assertSame(48.5, $point->getLatitude());
    }

    /** Technical input rejection preserves receiver. */
    public function testTechnicalInputRejectionPreservesReceiver(): void
    {
        $point = new Point(1, 2);

        try {
            new \ReflectionMethod($point, 'setX')->invoke($point, []);
            self::fail('Arrays are not coordinate input.');
        } catch (\TypeError) {
            self::assertSame([1, 2], $point->toArray());
        }
    }

    /** Unavailable elevation rejects null without mutation. */
    public function testUnavailableElevationRejectsNullWithoutMutation(): void
    {
        $point = new Point(1, 2);

        try {
            $point->setZ(null);
            self::fail('An unavailable axis must be rejected.');
        } catch (BadMethodCallException) {
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        }
    }

    /** Unavailable elevation rejects number without mutation. */
    public function testUnavailableElevationRejectsNumberWithoutMutation(): void
    {
        $point = new Point(1, 2);

        try {
            $point->setZ(9);
            self::fail('An unavailable axis must be rejected.');
        } catch (BadMethodCallException) {
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        }
    }

    /** Unavailable measure rejects null without mutation. */
    public function testUnavailableMeasureRejectsNullWithoutMutation(): void
    {
        $point = new Point(1, 2);

        try {
            $point->setM(null);
            self::fail('An unavailable axis must be rejected.');
        } catch (BadMethodCallException) {
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        }
    }

    /** Unavailable measure rejects number without mutation. */
    public function testUnavailableMeasureRejectsNumberWithoutMutation(): void
    {
        $point = new Point(1, 2);

        try {
            $point->setM(9);
            self::fail('An unavailable axis must be rejected.');
        } catch (BadMethodCallException) {
            self::assertSame([1, 2], $point->toArray());
            self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        }
    }
}
