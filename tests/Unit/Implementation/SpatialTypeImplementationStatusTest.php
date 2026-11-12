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
     * BRepSolid implementation status is explicit.
     */
    public function testBrepSolid(): void
    {
        self::assertTrue(GeometryTypeEnum::BREPSOLID->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::BREPSOLID));
    }

    /**
     * Circle implementation status is explicit.
     */
    public function testCircle(): void
    {
        self::assertTrue(GeometryTypeEnum::CIRCLE->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::CIRCLE));
    }

    /**
     * CircularString implementation status is explicit.
     */
    public function testCircularString(): void
    {
        self::assertTrue(GeometryTypeEnum::CIRCULARSTRING->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::CIRCULARSTRING));
    }

    /**
     * Clothoid implementation status is explicit.
     */
    public function testClothoid(): void
    {
        self::assertTrue(GeometryTypeEnum::CLOTHOID->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::CLOTHOID));
    }

    /**
     * CompoundCurve implementation status is explicit.
     */
    public function testCompoundCurve(): void
    {
        self::assertTrue(GeometryTypeEnum::COMPOUNDCURVE->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::COMPOUNDCURVE));
    }

    /**
     * CompoundSurface implementation status is explicit.
     */
    public function testCompoundSurface(): void
    {
        self::assertTrue(GeometryTypeEnum::COMPOUNDSURFACE->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::COMPOUNDSURFACE));
    }

    /**
     * Curve has no implementation status because it is non-instantiable.
     */
    public function testCurveIsRejected(): void
    {
        self::assertFalse(GeometryTypeEnum::CURVE->isInstantiable());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Implementation status is not applicable to non-instantiable GeometryTypeEnum::CURVE.');

        SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::CURVE);
    }

    /**
     * CurvePolygon implementation status is explicit.
     */
    public function testCurvePolygon(): void
    {
        self::assertTrue(GeometryTypeEnum::CURVEPOLYGON->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::CURVEPOLYGON));
    }

    /**
     * EllipticalCurve implementation status is explicit.
     */
    public function testEllipticalCurve(): void
    {
        self::assertTrue(GeometryTypeEnum::ELLIPTICALCURVE->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::ELLIPTICALCURVE));
    }

    /**
     * An enum addition requires an explicit status and a dedicated test.
     */
    public function testEveryEnumCaseIsCovered(): void
    {
        self::assertSame([
            'BREPSOLID',
            'CIRCLE',
            'CIRCULARSTRING',
            'CLOTHOID',
            'COMPOUNDCURVE',
            'COMPOUNDSURFACE',
            'CURVE',
            'CURVEPOLYGON',
            'ELLIPTICALCURVE',
            'GEODESICSTRING',
            'GEOMETRY',
            'GEOMETRYCOLLECTION',
            'LINESTRING',
            'MULTICURVE',
            'MULTILINESTRING',
            'MULTIPOINT',
            'MULTIPOLYGON',
            'MULTISURFACE',
            'NURBSCURVE',
            'POINT',
            'POLYGON',
            'POLYHEDRALSURFACE',
            'SOLID',
            'SPIRALCURVE',
            'SURFACE',
            'TIN',
            'TRIANGLE',
        ], array_map(static fn (GeometryTypeEnum $type): string => $type->name, GeometryTypeEnum::cases()));
    }

    /**
     * GeodesicString implementation status is explicit.
     */
    public function testGeodesicString(): void
    {
        self::assertTrue(GeometryTypeEnum::GEODESICSTRING->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::GEODESICSTRING));
    }

    /**
     * GeometryCollection implementation status is explicit.
     */
    public function testGeometryCollection(): void
    {
        self::assertTrue(GeometryTypeEnum::GEOMETRYCOLLECTION->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::GEOMETRYCOLLECTION));
    }

    /**
     * Geometry has no implementation status because it is non-instantiable.
     */
    public function testGeometryIsRejected(): void
    {
        self::assertFalse(GeometryTypeEnum::GEOMETRY->isInstantiable());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Implementation status is not applicable to non-instantiable GeometryTypeEnum::GEOMETRY.');

        SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::GEOMETRY);
    }

    /**
     * LineString implementation status is explicit.
     */
    public function testLineString(): void
    {
        self::assertTrue(GeometryTypeEnum::LINESTRING->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::LINESTRING));
    }

    /**
     * MultiCurve implementation status is explicit.
     */
    public function testMultiCurve(): void
    {
        self::assertTrue(GeometryTypeEnum::MULTICURVE->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTICURVE));
    }

    /**
     * MultiLineString implementation status is explicit.
     */
    public function testMultiLineString(): void
    {
        self::assertTrue(GeometryTypeEnum::MULTILINESTRING->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTILINESTRING));
    }

    /**
     * MultiPoint implementation status is explicit.
     */
    public function testMultiPoint(): void
    {
        self::assertTrue(GeometryTypeEnum::MULTIPOINT->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTIPOINT));
    }

    /**
     * MultiPolygon implementation status is explicit.
     */
    public function testMultiPolygon(): void
    {
        self::assertTrue(GeometryTypeEnum::MULTIPOLYGON->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTIPOLYGON));
    }

    /**
     * MultiSurface implementation status is explicit.
     */
    public function testMultiSurface(): void
    {
        self::assertTrue(GeometryTypeEnum::MULTISURFACE->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::MULTISURFACE));
    }

    /**
     * Every enum case has a status or the expected non-instantiable rejection.
     */
    public function testNoEnumCaseHasUndefinedImplementationStatus(): void
    {
        foreach (GeometryTypeEnum::cases() as $type) {
            try {
                SpatialTypeImplementationStatus::isFullyImplemented($type);
                self::assertTrue($type->isInstantiable(), $type->name);
            } catch (InvalidValueException) {
                self::assertFalse($type->isInstantiable(), $type->name);
            } catch (LogicException $exception) {
                self::fail($exception->getMessage());
            }
        }
    }

    /**
     * NurbsCurve implementation status is explicit.
     */
    public function testNurbsCurve(): void
    {
        self::assertTrue(GeometryTypeEnum::NURBSCURVE->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::NURBSCURVE));
    }

    /**
     * Point implementation status is explicit.
     */
    public function testPoint(): void
    {
        self::assertTrue(GeometryTypeEnum::POINT->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::POINT));
    }

    /**
     * Polygon implementation status is explicit.
     */
    public function testPolygon(): void
    {
        self::assertTrue(GeometryTypeEnum::POLYGON->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::POLYGON));
    }

    /**
     * PolyhedralSurface implementation status is explicit.
     */
    public function testPolyhedralSurface(): void
    {
        self::assertTrue(GeometryTypeEnum::POLYHEDRALSURFACE->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::POLYHEDRALSURFACE));
    }

    /**
     * Solid has no implementation status because it is non-instantiable.
     */
    public function testSolidIsRejected(): void
    {
        self::assertFalse(GeometryTypeEnum::SOLID->isInstantiable());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Implementation status is not applicable to non-instantiable GeometryTypeEnum::SOLID.');

        SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::SOLID);
    }

    /**
     * SpiralCurve implementation status is explicit.
     */
    public function testSpiralCurve(): void
    {
        self::assertTrue(GeometryTypeEnum::SPIRALCURVE->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::SPIRALCURVE));
    }

    /**
     * Surface has no implementation status because it is non-instantiable.
     */
    public function testSurfaceIsRejected(): void
    {
        self::assertFalse(GeometryTypeEnum::SURFACE->isInstantiable());
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Implementation status is not applicable to non-instantiable GeometryTypeEnum::SURFACE.');

        SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::SURFACE);
    }

    /**
     * Tin implementation status is explicit.
     */
    public function testTin(): void
    {
        self::assertTrue(GeometryTypeEnum::TIN->isInstantiable());
        self::assertFalse(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::TIN));
    }

    /**
     * Triangle implementation status is explicit.
     */
    public function testTriangle(): void
    {
        self::assertTrue(GeometryTypeEnum::TRIANGLE->isInstantiable());
        self::assertTrue(SpatialTypeImplementationStatus::isFullyImplemented(GeometryTypeEnum::TRIANGLE));
    }
}
