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
use LongitudeOne\SpatialTypes\Types\AbstractSpatialType;
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
        foreach ((new \ReflectionClass(AbstractSpatialType::class))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            static::assertStringStartsNotWith('with', $method->getName());
            static::assertStringStartsNotWith('set', $method->getName());
        }
    }

    /** All Dimension2 Geography concrete immutable types are final. */
    public function testDimension2GeographyClassesAreFinal(): void
    {
        static::assertTrue((new \ReflectionClass(CircularStringImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(CompoundCurveImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(GeographyCollectionImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(LineStringImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(MultiLineStringImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(MultiPointImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(MultiPolygonImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(PointImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(PolygonImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(TriangleImmutable::class))->isFinal());
    }

    /** All Dimension2 Geometry concrete immutable types are final. */
    public function testDimension2GeometryClassesAreFinal(): void
    {
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurve'));
        static::assertTrue((new \ReflectionClass(GeometryCollectionImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPoint'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\TriangleImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Triangle'));
    }

    /** All Dimension3m Geography concrete immutable types are final. */
    public function testDimension3mGeographyClassesAreFinal(): void
    {
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CircularString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurveImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\CompoundCurve'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollectionImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\GeographyCollection'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiLineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPoint'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Point'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Polygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\TriangleImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Triangle'));
    }

    /** All Dimension3m Geometry concrete immutable types are final. */
    public function testDimension3mGeometryClassesAreFinal(): void
    {
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurveImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CompoundCurve'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollectionImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\GeometryCollection'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiLineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPoint'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Polygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\TriangleImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Triangle'));
    }

    /** All Dimension3z Geography concrete immutable types are final. */
    public function testDimension3zGeographyClassesAreFinal(): void
    {
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CircularStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CircularString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CompoundCurveImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\CompoundCurve'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollectionImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\GeographyCollection'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPoint'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPolygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Polygon'));
        static::assertTrue((new \ReflectionClass(PolyhedralSurfaceImmutable::class))->isFinal());
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\TriangleImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Triangle'));
    }

    /** All Dimension3z Geometry concrete immutable types are final. */
    public function testDimension3zGeometryClassesAreFinal(): void
    {
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CompoundCurveImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CompoundCurve'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollectionImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\GeometryCollection'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPoint'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPolygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Polygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurfaceImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurface'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\TriangleImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Triangle'));
    }

    /** All Dimension4zm Geography concrete immutable types are final. */
    public function testDimension4zmGeographyClassesAreFinal(): void
    {
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurveImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurve'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollection'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPoint'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Point'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Polygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurfaceImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurface'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\TriangleImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Triangle'));
    }

    /** All Dimension4zm Geometry concrete immutable types are final. */
    public function testDimension4zmGeometryClassesAreFinal(): void
    {
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CircularStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CircularString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurveImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\CompoundCurve'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollectionImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\GeometryCollection'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineStringImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineString'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPoint'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Point'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Polygon'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolyhedralSurfaceImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolyhedralSurface'));
        static::assertTrue((new \ReflectionClass(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\TriangleImmutable::class))->isFinal());
        static::assertFalse(class_exists('LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Triangle'));
    }

    /** Mutable metadata setters belong to the mutable spatial contract. */
    public function testMutableMetadataContract(): void
    {
        $contract = new \ReflectionClass(MutableSpatialInterface::class);
        static::assertTrue($contract->hasMethod('setFamily'));
        static::assertTrue($contract->hasMethod('setSpatialReference'));
        static::assertTrue($contract->hasMethod('setSrid'));
        static::assertFalse($contract->hasMethod('setDimension'));
        static::assertFalse($contract->hasMethod('withSrid'));
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
            foreach ((new \ReflectionClass($contract))->getMethods() as $method) {
                static::assertStringStartsNotWith('with', $method->getName());
                static::assertNotSame('setDimension', $method->getName());
            }
        }
    }
}
