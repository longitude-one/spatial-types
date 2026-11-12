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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableCurveInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Compound-curve continuity and component invariants.
 *
 * @internal
 *
 * @coversNothing
 */
class CompoundCurveValidationTest extends TestCase
{
    /** Multiple arcs preserve their separate circular-string components. */
    public function testCircularComponents(): void
    {
        $compound = new CompoundCurveImmutable([
            new CircularStringImmutable([[0, 0], [1, 1], [2, 0]]),
            new CircularStringImmutable([[2, 0], [3, -1], [4, 0]]),
        ]);

        self::assertInstanceOf(CircularStringImmutable::class, $compound->getCurve(0));
        self::assertInstanceOf(CircularStringImmutable::class, $compound->getCurve(1));
        self::assertSame([[[0, 0], [1, 1], [2, 0]], [[2, 0], [3, -1], [4, 0]]], $compound->toArray());
    }

    /** A complete circle preserves its equal endpoint representation. */
    public function testCircularStringPreservesEqualEndpoints(): void
    {
        $circle = new CircularStringImmutable([[1, 0], [-1, 0], [1, 0]]);

        self::assertSame($circle->getPoint(0), $circle->getStartPoint());
        self::assertSame($circle->getPoint(-1), $circle->getEndPoint());
    }

    /** A closed line exposes the same endpoints as its point accessors. */
    public function testClosedLineEndpoints(): void
    {
        $line = new LineStringImmutable([[0, 0], [1, 1], [0, 0]]);

        self::assertSame($line->getPoint(0), $line->getStartPoint());
        self::assertSame($line->getPoint(-1), $line->getEndPoint());
    }

    /** Components need only the immutable curve contract, not concrete interfaces. */
    public function testCommonCurveImplementation(): void
    {
        $component = self::createStub(ImmutableCurveInterface::class);
        $component->method('getType')->willReturn(GeometryTypeEnum::LINESTRING);
        $component->method('getDimension')->willReturn(CoordinateDimensionEnum::XY);
        $component->method('hasZ')->willReturn(false);
        $component->method('hasM')->willReturn(false);
        $component->method('getFamily')->willReturn(SpatialModelEnum::GEOMETRY);
        $component->method('getSpatialReference')->willReturn(SpatialReference::fromSrid(0));
        $component->method('getStartPoint')->willReturn(new PointImmutable(0, 0));
        $component->method('getEndPoint')->willReturn(new PointImmutable(1, 1));
        $component->method('toArray')->willReturn([[0, 0], [1, 1]]);
        $compound = new CompoundCurveImmutable([$component]);

        self::assertSame($component, $compound->getCurve(0));
        self::assertSame([[[0, 0], [1, 1]]], $compound->toArray());
    }

    /** Booleans are not normalized into numeric component ordinates. */
    public function testComponentRejectsBooleanOrdinate(): void
    {
        $component = self::createStub(ImmutableCurveInterface::class);
        $component->method('toArray')->willReturn([[0, false]]);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('A compound curve component must expose numeric or null ordinates.');

        new CompoundCurveImmutable([$component]);
    }

    /** Strings remain invalid component ordinates when null is accepted. */
    public function testComponentRejectsNumericStringOrdinate(): void
    {
        $component = self::createStub(ImmutableCurveInterface::class);
        $component->method('toArray')->willReturn([[0, '2']]);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('A compound curve component must expose numeric or null ordinates.');

        new CompoundCurveImmutable([$component]);
    }

    /** Indexed access follows the existing empty-aggregate exception contract. */
    public function testEmptyIndexedAccess(): void
    {
        $this->expectException(OutOfBoundsException::class);

        new CompoundCurveImmutable()->getCurve(0);
    }

    /** Nested component coordinates cannot masquerade as point ordinates. */
    public function testInvalidComponentOrdinate(): void
    {
        $component = self::createStub(ImmutableCurveInterface::class);
        $component->method('getType')->willReturn(GeometryTypeEnum::LINESTRING);
        $component->method('getDimension')->willReturn(CoordinateDimensionEnum::XY);
        $component->method('hasZ')->willReturn(false);
        $component->method('hasM')->willReturn(false);
        $component->method('getFamily')->willReturn(SpatialModelEnum::GEOMETRY);
        $component->method('getSpatialReference')->willReturn(SpatialReference::fromSrid(0));
        $component->method('getStartPoint')->willReturn(new PointImmutable(0, 0));
        $component->method('getEndPoint')->willReturn(new PointImmutable(1, 1));
        $component->method('toArray')->willReturn([[[0, 0]], [[1, 1]]]);
        $this->expectException(InvalidValueException::class);

        new CompoundCurveImmutable([$component]);
    }

    /** A component violating its tuple representation is rejected on construction. */
    public function testInvalidComponentTuple(): void
    {
        $component = self::createStub(ImmutableCurveInterface::class);
        $component->method('getType')->willReturn(GeometryTypeEnum::LINESTRING);
        $component->method('getDimension')->willReturn(CoordinateDimensionEnum::XY);
        $component->method('hasZ')->willReturn(false);
        $component->method('hasM')->willReturn(false);
        $component->method('getFamily')->willReturn(SpatialModelEnum::GEOMETRY);
        $component->method('getSpatialReference')->willReturn(SpatialReference::fromSrid(0));
        $component->method('getStartPoint')->willReturn(new PointImmutable(0, 0));
        $component->method('getEndPoint')->willReturn(new PointImmutable(1, 1));
        $component->method('toArray')->willReturn([0, 1]);
        $this->expectException(InvalidValueException::class);

        new CompoundCurveImmutable([$component]);
    }

    /** Legacy reference changes copy both component endpoints. */
    public function testLegacyReferenceReplacement(): void
    {
        $compound = new CompoundCurveImmutable([new LineStringImmutable([[0, 0], [1, 1]])]);
        $copy = $compound->withSrid(4326);

        self::assertSame(0, $compound->getSrid());
        self::assertSame(0, $compound->getStartPoint()?->getSrid());
        self::assertSame(4326, $copy->getStartPoint()?->getSrid());
        self::assertSame(4326, $copy->getEndPoint()?->getSrid());
        self::assertSame([[[0, 0], [1, 1]]], $copy->toArray());
        self::assertSame([
            'type' => 'CompoundCurve',
            'coordinates' => [[[0, 0], [1, 1]]],
            'srid' => 4326,
        ], $copy->jsonSerialize());
    }

    /** An ordered sequence of line strings is an open compound curve. */
    public function testLinearComponents(): void
    {
        $compound = new CompoundCurveImmutable([
            new LineStringImmutable([[0, 0], [1, 1]]),
            new LineStringImmutable([[1, 1], [2, 0]]),
        ]);

        self::assertSame([0, 0], $compound->getStartPoint()?->toArray());
        self::assertSame([2, 0], $compound->getEndPoint()?->toArray());
        self::assertSame([[[0, 0], [1, 1]], [[1, 1], [2, 0]]], $compound->toArray());
    }

    /** Construction rejects values outside the common curve contract. */
    public function testNonCurve(): void
    {
        $this->expectException(InvalidValueException::class);

        new \ReflectionClass(CompoundCurveImmutable::class)->newInstance([new PointImmutable(0, 0)]);
    }

    /** Coordinate tuples do not implicitly select a component interpolation. */
    public function testRawCoordinates(): void
    {
        $this->expectException(InvalidValueException::class);

        new \ReflectionClass(CompoundCurveImmutable::class)->newInstance([[[0, 0], [1, 1]]]);
    }

    /** Returned membership arrays cannot mutate the compound curve. */
    public function testReturnedArrayIsIndependent(): void
    {
        $line = new LineStringImmutable([[0, 0], [1, 1]]);
        $compound = new CompoundCurveImmutable([$line]);
        $curves = $compound->getCurves();
        $curves[] = new LineStringImmutable([[1, 1], [2, 2]]);

        self::assertCount(2, $curves);
        self::assertSame([$line], $compound->getCurves());
    }
}
