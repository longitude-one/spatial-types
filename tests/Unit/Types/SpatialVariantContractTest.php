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

use LongitudeOne\SpatialTypes\Interfaces\CircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\CollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\CompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\CurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MultiLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MultiPolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCollectionInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiPolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutablePolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableTriangleInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\PolyhedralSurfaceInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\TriangleInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialType;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\TriangleImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolyhedralSurfaceImmutable;
use PHPUnit\Framework\TestCase;

/** Public variant contract boundaries.
 * @internal
 *
 * @coversNothing
 */
class SpatialVariantContractTest extends TestCase
{
    /** The neutral base does not leak immutable replacement operations. */
    public function testCommonBaseExposesReadMethodsOnly(): void
    {
        foreach (new \ReflectionClass(AbstractSpatialType::class)->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            self::assertStringStartsNotWith('with', $method->getName());
            self::assertStringStartsNotWith('set', $method->getName());
        }
    }

    /** All Dimension2 Geography concrete immutable types are final. */
    public function testDimension2GeographyClassesAreFinal(): void
    {
        self::assertTrue(new \ReflectionClass(CircularStringImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(CompoundCurveImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(GeographyCollectionImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(LineStringImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(MultiLineStringImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(MultiPointImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(MultiPolygonImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(PointImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(PolygonImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(TriangleImmutable::class)->isFinal());
    }

    /** All Dimension2 Geometry concrete immutable types are final. */
    public function testDimension2GeometryClassesAreFinal(): void
    {
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurve'));
        self::assertTrue(new \ReflectionClass(GeometryCollectionImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPoint'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\TriangleImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Triangle'));
    }

    /** All Dimension3m Geography concrete immutable types are final. */
    public function testDimension3mGeographyClassesAreFinal(): void
    {
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurveImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurve'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollection'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPoint'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Polygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\TriangleImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Triangle'));
    }

    /** All Dimension3m Geometry concrete immutable types are final. */
    public function testDimension3mGeometryClassesAreFinal(): void
    {
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurveImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurve'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollection'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPoint'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Polygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\TriangleImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Triangle'));
    }

    /** All Dimension3z Geography concrete immutable types are final. */
    public function testDimension3zGeographyClassesAreFinal(): void
    {
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CircularStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CircularString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CompoundCurveImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CompoundCurve'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollectionImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollection'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPoint'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPolygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Polygon'));
        self::assertTrue(new \ReflectionClass(PolyhedralSurfaceImmutable::class)->isFinal());
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\TriangleImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Triangle'));
    }

    /** All Dimension3z Geometry concrete immutable types are final. */
    public function testDimension3zGeometryClassesAreFinal(): void
    {
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CompoundCurveImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CompoundCurve'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollectionImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollection'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPoint'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Polygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurfaceImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurface'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\TriangleImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Triangle'));
    }

    /** All Dimension4zm Geography concrete immutable types are final. */
    public function testDimension4zmGeographyClassesAreFinal(): void
    {
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurveImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurve'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollection'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPoint'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Point'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Polygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurfaceImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurface'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\TriangleImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Triangle'));
    }

    /** All Dimension4zm Geometry concrete immutable types are final. */
    public function testDimension4zmGeometryClassesAreFinal(): void
    {
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CircularStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CircularString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurveImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurve'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollection'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineStringImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineString'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPoint'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Point'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Polygon'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolyhedralSurfaceImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolyhedralSurface'));
        self::assertTrue(new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\TriangleImmutable::class)->isFinal());
        self::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Triangle'));
    }

    /** Mutable metadata setters belong to the mutable spatial contract. */
    public function testMutableMetadataContract(): void
    {
        $contract = new \ReflectionClass(MutableSpatialInterface::class);
        self::assertTrue($contract->hasMethod('setFamily'));
        self::assertTrue($contract->hasMethod('setSpatialReference'));
        self::assertTrue($contract->hasMethod('setSrid'));
        self::assertFalse($contract->hasMethod('setDimension'));
        self::assertFalse($contract->hasMethod('withSrid'));
    }

    /** Common and mutable contracts expose no copy operations. */
    public function testReadAndMutableContractsExposeNoCopyMethods(): void
    {
        $contracts = [
            CircularStringInterface::class,
            CollectionInterface::class,
            CompoundCurveInterface::class,
            CurveInterface::class,
            LineStringInterface::class,
            MultiLineStringInterface::class,
            MultiPointInterface::class,
            MultiPolygonInterface::class,
            MutableCircularStringInterface::class,
            MutableCollectionInterface::class,
            MutableCompoundCurveInterface::class,
            MutableCurveInterface::class,
            MutableLineStringInterface::class,
            MutableMultiLineStringInterface::class,
            MutableMultiPointInterface::class,
            MutableMultiPolygonInterface::class,
            MutablePointInterface::class,
            MutablePolygonInterface::class,
            MutablePolyhedralSurfaceInterface::class,
            MutableSpatialInterface::class,
            MutableTriangleInterface::class,
            PointInterface::class,
            PolygonInterface::class,
            PolyhedralSurfaceInterface::class,
            SpatialInterface::class,
            TriangleInterface::class,
        ];
        foreach ($contracts as $contract) {
            foreach (new \ReflectionClass($contract)->getMethods() as $method) {
                self::assertStringStartsNotWith('with', $method->getName());
                self::assertNotSame('setDimension', $method->getName());
            }
        }
    }
}
