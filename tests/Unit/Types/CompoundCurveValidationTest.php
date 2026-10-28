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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\CurveInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
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
        $compound = new CompoundCurve([
            new CircularString([[0, 0], [1, 1], [2, 0]]),
            new CircularString([[2, 0], [3, -1], [4, 0]]),
        ]);

        static::assertFalse($compound->isClosed());
        static::assertInstanceOf(CircularString::class, $compound->getCurve(0));
        static::assertInstanceOf(CircularString::class, $compound->getCurve(1));
        static::assertSame([[[0, 0], [1, 1], [2, 0]], [[2, 0], [3, -1], [4, 0]]], $compound->toArray());
    }

    /** A closed line exposes the same endpoints as its point accessors. */
    public function testClosedLineEndpoints(): void
    {
        $line = new LineString([[0, 0], [1, 1], [0, 0]]);

        static::assertTrue($line->isClosed());
        static::assertSame($line->getPoint(0), $line->getStartPoint());
        static::assertSame($line->getPoint(-1), $line->getEndPoint());
    }

    /** Components need only the common curve contract, not concrete interfaces. */
    public function testCommonCurveImplementation(): void
    {
        $component = static::createStub(CurveInterface::class);
        $component->method('getType')->willReturn(GeometryTypeEnum::LINESTRING);
        $component->method('getDimension')->willReturn(CoordinateDimensionEnum::XY);
        $component->method('hasZ')->willReturn(false);
        $component->method('hasM')->willReturn(false);
        $component->method('getFamily')->willReturn(SpatialModelEnum::GEOMETRY);
        $component->method('getSpatialReference')->willReturn(SpatialReference::fromSrid(0));
        $component->method('isEmpty')->willReturn(false);
        $component->method('getStartPoint')->willReturn(new Point(0, 0));
        $component->method('getEndPoint')->willReturn(new Point(1, 1));
        $component->method('toArray')->willReturn([[0, 0], [1, 1]]);
        $compound = new CompoundCurve([$component]);

        static::assertSame($component, $compound->getCurve(0));
        static::assertSame([[[0, 0], [1, 1]]], $compound->toArray());
        static::assertFalse($compound->isClosed());
    }

    /** Booleans are not normalized into numeric component ordinates. */
    public function testComponentRejectsBooleanOrdinate(): void
    {
        $component = static::createStub(CurveInterface::class);
        $component->method('toArray')->willReturn([[0, false]]);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('A compound curve component must expose numeric or null ordinates.');

        new CompoundCurve([$component]);
    }

    /** Strings remain invalid component ordinates when null is accepted. */
    public function testComponentRejectsNumericStringOrdinate(): void
    {
        $component = static::createStub(CurveInterface::class);
        $component->method('toArray')->willReturn([[0, '2']]);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('A compound curve component must expose numeric or null ordinates.');

        new CompoundCurve([$component]);
    }

    /** Indexed access follows the existing empty-aggregate exception contract. */
    public function testEmptyIndexedAccess(): void
    {
        $this->expectException(OutOfBoundsException::class);

        (new CompoundCurve())->getCurve(0);
    }

    /** Nested component coordinates cannot masquerade as point ordinates. */
    public function testInvalidComponentOrdinate(): void
    {
        $component = static::createStub(CurveInterface::class);
        $component->method('getType')->willReturn(GeometryTypeEnum::LINESTRING);
        $component->method('getDimension')->willReturn(CoordinateDimensionEnum::XY);
        $component->method('hasZ')->willReturn(false);
        $component->method('hasM')->willReturn(false);
        $component->method('getFamily')->willReturn(SpatialModelEnum::GEOMETRY);
        $component->method('getSpatialReference')->willReturn(SpatialReference::fromSrid(0));
        $component->method('isEmpty')->willReturn(false);
        $component->method('getStartPoint')->willReturn(new Point(0, 0));
        $component->method('getEndPoint')->willReturn(new Point(1, 1));
        $component->method('toArray')->willReturn([[[0, 0]], [[1, 1]]]);
        $this->expectException(InvalidValueException::class);

        new CompoundCurve([$component]);
    }

    /** A component violating its tuple representation is rejected on construction. */
    public function testInvalidComponentTuple(): void
    {
        $component = static::createStub(CurveInterface::class);
        $component->method('getType')->willReturn(GeometryTypeEnum::LINESTRING);
        $component->method('getDimension')->willReturn(CoordinateDimensionEnum::XY);
        $component->method('hasZ')->willReturn(false);
        $component->method('hasM')->willReturn(false);
        $component->method('getFamily')->willReturn(SpatialModelEnum::GEOMETRY);
        $component->method('getSpatialReference')->willReturn(SpatialReference::fromSrid(0));
        $component->method('isEmpty')->willReturn(false);
        $component->method('getStartPoint')->willReturn(new Point(0, 0));
        $component->method('getEndPoint')->willReturn(new Point(1, 1));
        $component->method('toArray')->willReturn([0, 1]);
        $this->expectException(InvalidValueException::class);

        new CompoundCurve([$component]);
    }

    /** Legacy reference changes copy both component endpoints. */
    public function testLegacyReferenceReplacement(): void
    {
        $compound = new CompoundCurve([new LineString([[0, 0], [1, 1]])]);
        $copy = $compound->withSrid(4326);

        static::assertSame(0, $compound->getSrid());
        static::assertSame(0, $compound->getStartPoint()?->getSrid());
        static::assertSame(4326, $copy->getStartPoint()?->getSrid());
        static::assertSame(4326, $copy->getEndPoint()?->getSrid());
        static::assertSame([[[0, 0], [1, 1]]], $copy->toArray());
        static::assertSame([
            'type' => 'CompoundCurve',
            'coordinates' => [[[0, 0], [1, 1]]],
            'srid' => 4326,
        ], $copy->jsonSerialize());
    }

    /** An ordered sequence of line strings is an open compound curve. */
    public function testLinearComponents(): void
    {
        $compound = new CompoundCurve([
            new LineString([[0, 0], [1, 1]]),
            new LineString([[1, 1], [2, 0]]),
        ]);

        static::assertFalse($compound->isClosed());
        static::assertSame([0, 0], $compound->getStartPoint()?->toArray());
        static::assertSame([2, 0], $compound->getEndPoint()?->toArray());
        static::assertSame([[[0, 0], [1, 1]], [[1, 1], [2, 0]]], $compound->toArray());
    }

    /** Construction rejects values outside the common curve contract. */
    public function testNonCurve(): void
    {
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(CompoundCurve::class))->newInstance([new Point(0, 0)]);
    }

    /** Coordinate tuples do not implicitly select a component interpolation. */
    public function testRawCoordinates(): void
    {
        $this->expectException(InvalidValueException::class);

        (new \ReflectionClass(CompoundCurve::class))->newInstance([[[0, 0], [1, 1]]]);
    }

    /** Returned membership arrays cannot mutate the compound curve. */
    public function testReturnedArrayIsIndependent(): void
    {
        $line = new LineString([[0, 0], [1, 1]]);
        $compound = new CompoundCurve([$line]);
        $curves = $compound->getCurves();
        $curves[] = new LineString([[1, 1], [2, 2]]);

        static::assertCount(2, $curves);
        static::assertSame([$line], $compound->getCurves());
    }

    /** A complete circle forms a closed single-component compound. */
    public function testSingleClosedComponent(): void
    {
        $circle = new CircularString([[1, 0], [-1, 0], [1, 0]]);
        $compound = new CompoundCurve([$circle]);

        static::assertTrue($circle->isClosed());
        static::assertTrue($compound->isClosed());
        static::assertSame($circle->getPoint(0), $circle->getStartPoint());
        static::assertSame($circle->getPoint(-1), $circle->getEndPoint());
    }
}
