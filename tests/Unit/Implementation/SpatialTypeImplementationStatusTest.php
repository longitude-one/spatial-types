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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Implementation;

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\LogicException;
use LongitudeOne\SpatialTypes\Implementation\SpatialTypeImplementationStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Implementation status of each model geometry type.
 *
 * @internal
 */
#[CoversClass(SpatialTypeImplementationStatus::class)]
class SpatialTypeImplementationStatusTest extends TestCase
{
    /**
     * CircularString implementation status is explicit.
     */
    public function testCircularString(): void
    {
        static::assertTrue(GeometryTypeEnum::CIRCULARSTRING->isInstantiable());
        static::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::CIRCULARSTRING));
    }

    /**
     * CompoundCurve implementation status is explicit.
     */
    public function testCompoundCurve(): void
    {
        static::assertTrue(GeometryTypeEnum::COMPOUNDCURVE->isInstantiable());
        static::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::COMPOUNDCURVE));
    }

    /**
     * Curve has no implementation status because it is non-instantiable.
     */
    public function testCurveIsRejected(): void
    {
        static::assertFalse(GeometryTypeEnum::CURVE->isInstantiable());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Implementation status is not applicable to non-instantiable GeometryTypeEnum::CURVE.');

        SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::CURVE);
    }

    /**
     * CurvePolygon implementation status is explicit.
     */
    public function testCurvePolygon(): void
    {
        static::assertTrue(GeometryTypeEnum::CURVEPOLYGON->isInstantiable());
        static::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::CURVEPOLYGON));
    }

    /**
     * An enum addition requires an explicit status and a dedicated test.
     */
    public function testEveryEnumCaseIsCovered(): void
    {
        static::assertSame([
            'CIRCULARSTRING',
            'COMPOUNDCURVE',
            'CURVE',
            'CURVEPOLYGON',
            'GEOMETRY',
            'GEOMETRYCOLLECTION',
            'LINESTRING',
            'MULTICURVE',
            'MULTILINESTRING',
            'MULTIPOINT',
            'MULTIPOLYGON',
            'MULTISURFACE',
            'POINT',
            'POLYGON',
            'POLYHEDRALSURFACE',
            'SOLID',
            'SURFACE',
            'TIN',
            'TRIANGLE',
        ], array_map(static fn (GeometryTypeEnum $type): string => $type->name, GeometryTypeEnum::cases()));
    }

    /**
     * GeometryCollection implementation status is explicit.
     */
    public function testGeometryCollection(): void
    {
        static::assertTrue(GeometryTypeEnum::GEOMETRYCOLLECTION->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::GEOMETRYCOLLECTION));
    }

    /**
     * Geometry has no implementation status because it is non-instantiable.
     */
    public function testGeometryIsRejected(): void
    {
        static::assertFalse(GeometryTypeEnum::GEOMETRY->isInstantiable());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Implementation status is not applicable to non-instantiable GeometryTypeEnum::GEOMETRY.');

        SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::GEOMETRY);
    }

    /**
     * LineString implementation status is explicit.
     */
    public function testLineString(): void
    {
        static::assertTrue(GeometryTypeEnum::LINESTRING->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::LINESTRING));
    }

    /**
     * MultiCurve implementation status is explicit.
     */
    public function testMultiCurve(): void
    {
        static::assertTrue(GeometryTypeEnum::MULTICURVE->isInstantiable());
        static::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTICURVE));
    }

    /**
     * MultiLineString implementation status is explicit.
     */
    public function testMultiLineString(): void
    {
        static::assertTrue(GeometryTypeEnum::MULTILINESTRING->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTILINESTRING));
    }

    /**
     * MultiPoint implementation status is explicit.
     */
    public function testMultiPoint(): void
    {
        static::assertTrue(GeometryTypeEnum::MULTIPOINT->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTIPOINT));
    }

    /**
     * MultiPolygon implementation status is explicit.
     */
    public function testMultiPolygon(): void
    {
        static::assertTrue(GeometryTypeEnum::MULTIPOLYGON->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTIPOLYGON));
    }

    /**
     * MultiSurface implementation status is explicit.
     */
    public function testMultiSurface(): void
    {
        static::assertTrue(GeometryTypeEnum::MULTISURFACE->isInstantiable());
        static::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTISURFACE));
    }

    /**
     * Every enum case has a status or the expected non-instantiable rejection.
     */
    public function testNoEnumCaseHasUndefinedImplementationStatus(): void
    {
        foreach (GeometryTypeEnum::cases() as $type) {
            try {
                SpatialTypeImplementationStatus::isFullyImplemented($type);
                static::assertTrue($type->isInstantiable(), $type->name);
            } catch (InvalidValueException) {
                static::assertFalse($type->isInstantiable(), $type->name);
            } catch (LogicException $exception) {
                static::fail($exception->getMessage());
            }
        }
    }

    /**
     * Point implementation status is explicit.
     */
    public function testPoint(): void
    {
        static::assertTrue(GeometryTypeEnum::POINT->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::POINT));
    }

    /**
     * Polygon implementation status is explicit.
     */
    public function testPolygon(): void
    {
        static::assertTrue(GeometryTypeEnum::POLYGON->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::POLYGON));
    }

    /**
     * PolyhedralSurface implementation status is explicit.
     */
    public function testPolyhedralSurface(): void
    {
        static::assertTrue(GeometryTypeEnum::POLYHEDRALSURFACE->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::POLYHEDRALSURFACE));
    }

    /**
     * Solid has no implementation status because it is non-instantiable.
     */
    public function testSolidIsRejected(): void
    {
        static::assertFalse(GeometryTypeEnum::SOLID->isInstantiable());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Implementation status is not applicable to non-instantiable GeometryTypeEnum::SOLID.');

        SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::SOLID);
    }

    /**
     * Surface has no implementation status because it is non-instantiable.
     */
    public function testSurfaceIsRejected(): void
    {
        static::assertFalse(GeometryTypeEnum::SURFACE->isInstantiable());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Implementation status is not applicable to non-instantiable GeometryTypeEnum::SURFACE.');

        SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::SURFACE);
    }

    /**
     * Tin implementation status is explicit.
     */
    public function testTin(): void
    {
        static::assertTrue(GeometryTypeEnum::TIN->isInstantiable());
        static::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::TIN));
    }

    /**
     * Triangle implementation status is explicit.
     */
    public function testTriangle(): void
    {
        static::assertTrue(GeometryTypeEnum::TRIANGLE->isInstantiable());
        static::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::TRIANGLE));
    }
}
