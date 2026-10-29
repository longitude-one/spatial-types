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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory\PublicFactory;

use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography as GeographyXYFactories;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geometry as GeometryXYFactories;
use LongitudeOne\SpatialTypes\Factory\Dimension3m\Geography as GeographyXYMFactories;
use LongitudeOne\SpatialTypes\Factory\Dimension3m\Geometry as GeometryXYMFactories;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography as GeographyXYZFactories;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geometry as GeometryXYZFactories;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography as GeographyXYZMFactories;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geometry as GeometryXYZMFactories;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography as GeographyXYTypes;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry as GeometryXYTypes;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography as GeographyXYMTypes;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry as GeometryXYMTypes;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography as GeographyXYZTypes;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry as GeometryXYZTypes;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography as GeographyXYZMTypes;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry as GeometryXYZMTypes;
use PHPUnit\Framework\TestCase;

/** Verifies signature metadata without constructing dynamic spatial fixtures.
 * @internal
 *
 * @coversNothing
 */
final class FactorySignatureTest extends TestCase
{
    /** @param array<class-string, class-string> $factories Factory-to-constructor metadata */
    private static function assertSignatures(array $factories): void
    {
        foreach ($factories as $factory => $immutable) {
            $creation = new \ReflectionMethod($factory, 'create');
            $constructor = new \ReflectionMethod($immutable, '__construct');
            self::assertTrue($creation->isStatic());
            self::assertTrue($creation->isPublic());
            self::assertCount($constructor->getNumberOfParameters(), $creation->getParameters());
            foreach ($constructor->getParameters() as $position => $expected) {
                $actual = $creation->getParameters()[$position];
                self::assertSame($expected->getName(), $actual->getName(), $factory);
                self::assertSame((string) $expected->getType(), (string) $actual->getType(), $factory);
                self::assertSame($expected->isDefaultValueAvailable(), $actual->isDefaultValueAvailable(), $factory);
                if ($expected->isDefaultValueAvailable()) {
                    self::assertSame($expected->getDefaultValue(), $actual->getDefaultValue(), $factory);
                }
            }
        }
    }

    /** Every XY public factory mirrors its immutable constructor signature. */
    public function testXyCreationSignatures(): void
    {
        $factories = [
            GeometryXYFactories\PointFactory::class => GeometryXYTypes\PointImmutable::class,
            GeometryXYFactories\LineStringFactory::class => GeometryXYTypes\LineStringImmutable::class,
            GeometryXYFactories\CircularStringFactory::class => GeometryXYTypes\CircularStringImmutable::class,
            GeometryXYFactories\CompoundCurveFactory::class => GeometryXYTypes\CompoundCurveImmutable::class,
            GeometryXYFactories\PolygonFactory::class => GeometryXYTypes\PolygonImmutable::class,
            GeometryXYFactories\TriangleFactory::class => GeometryXYTypes\TriangleImmutable::class,
            GeometryXYFactories\MultiPointFactory::class => GeometryXYTypes\MultiPointImmutable::class,
            GeometryXYFactories\MultiLineStringFactory::class => GeometryXYTypes\MultiLineStringImmutable::class,
            GeometryXYFactories\MultiPolygonFactory::class => GeometryXYTypes\MultiPolygonImmutable::class,
            GeometryXYFactories\GeometryCollectionFactory::class => GeometryXYTypes\GeometryCollectionImmutable::class,
            GeographyXYFactories\PointFactory::class => GeographyXYTypes\PointImmutable::class,
            GeographyXYFactories\LineStringFactory::class => GeographyXYTypes\LineStringImmutable::class,
            GeographyXYFactories\CircularStringFactory::class => GeographyXYTypes\CircularStringImmutable::class,
            GeographyXYFactories\CompoundCurveFactory::class => GeographyXYTypes\CompoundCurveImmutable::class,
            GeographyXYFactories\PolygonFactory::class => GeographyXYTypes\PolygonImmutable::class,
            GeographyXYFactories\TriangleFactory::class => GeographyXYTypes\TriangleImmutable::class,
            GeographyXYFactories\MultiPointFactory::class => GeographyXYTypes\MultiPointImmutable::class,
            GeographyXYFactories\MultiLineStringFactory::class => GeographyXYTypes\MultiLineStringImmutable::class,
            GeographyXYFactories\MultiPolygonFactory::class => GeographyXYTypes\MultiPolygonImmutable::class,
            GeographyXYFactories\GeographyCollectionFactory::class => GeographyXYTypes\GeographyCollectionImmutable::class,
        ];
        self::assertCount(20, $factories);
        self::assertSignatures($factories);
    }

    /** Every XYM public factory mirrors its immutable constructor signature. */
    public function testXymCreationSignatures(): void
    {
        $factories = [
            GeometryXYMFactories\PointFactory::class => GeometryXYMTypes\PointImmutable::class,
            GeometryXYMFactories\LineStringFactory::class => GeometryXYMTypes\LineStringImmutable::class,
            GeometryXYMFactories\CircularStringFactory::class => GeometryXYMTypes\CircularStringImmutable::class,
            GeometryXYMFactories\CompoundCurveFactory::class => GeometryXYMTypes\CompoundCurveImmutable::class,
            GeometryXYMFactories\PolygonFactory::class => GeometryXYMTypes\PolygonImmutable::class,
            GeometryXYMFactories\TriangleFactory::class => GeometryXYMTypes\TriangleImmutable::class,
            GeometryXYMFactories\MultiPointFactory::class => GeometryXYMTypes\MultiPointImmutable::class,
            GeometryXYMFactories\MultiLineStringFactory::class => GeometryXYMTypes\MultiLineStringImmutable::class,
            GeometryXYMFactories\MultiPolygonFactory::class => GeometryXYMTypes\MultiPolygonImmutable::class,
            GeometryXYMFactories\GeometryCollectionFactory::class => GeometryXYMTypes\GeometryCollectionImmutable::class,
            GeographyXYMFactories\PointFactory::class => GeographyXYMTypes\PointImmutable::class,
            GeographyXYMFactories\LineStringFactory::class => GeographyXYMTypes\LineStringImmutable::class,
            GeographyXYMFactories\CircularStringFactory::class => GeographyXYMTypes\CircularStringImmutable::class,
            GeographyXYMFactories\CompoundCurveFactory::class => GeographyXYMTypes\CompoundCurveImmutable::class,
            GeographyXYMFactories\PolygonFactory::class => GeographyXYMTypes\PolygonImmutable::class,
            GeographyXYMFactories\TriangleFactory::class => GeographyXYMTypes\TriangleImmutable::class,
            GeographyXYMFactories\MultiPointFactory::class => GeographyXYMTypes\MultiPointImmutable::class,
            GeographyXYMFactories\MultiLineStringFactory::class => GeographyXYMTypes\MultiLineStringImmutable::class,
            GeographyXYMFactories\MultiPolygonFactory::class => GeographyXYMTypes\MultiPolygonImmutable::class,
            GeographyXYMFactories\GeographyCollectionFactory::class => GeographyXYMTypes\GeographyCollectionImmutable::class,
        ];
        self::assertCount(20, $factories);
        self::assertSignatures($factories);
    }

    /** Every XYZ public factory mirrors its immutable constructor signature. */
    public function testXyzCreationSignatures(): void
    {
        $factories = [
            GeometryXYZFactories\PointFactory::class => GeometryXYZTypes\PointImmutable::class,
            GeometryXYZFactories\LineStringFactory::class => GeometryXYZTypes\LineStringImmutable::class,
            GeometryXYZFactories\CircularStringFactory::class => GeometryXYZTypes\CircularStringImmutable::class,
            GeometryXYZFactories\CompoundCurveFactory::class => GeometryXYZTypes\CompoundCurveImmutable::class,
            GeometryXYZFactories\PolygonFactory::class => GeometryXYZTypes\PolygonImmutable::class,
            GeometryXYZFactories\TriangleFactory::class => GeometryXYZTypes\TriangleImmutable::class,
            GeometryXYZFactories\MultiPointFactory::class => GeometryXYZTypes\MultiPointImmutable::class,
            GeometryXYZFactories\MultiLineStringFactory::class => GeometryXYZTypes\MultiLineStringImmutable::class,
            GeometryXYZFactories\MultiPolygonFactory::class => GeometryXYZTypes\MultiPolygonImmutable::class,
            GeometryXYZFactories\PolyhedralSurfaceFactory::class => GeometryXYZTypes\PolyhedralSurfaceImmutable::class,
            GeometryXYZFactories\GeometryCollectionFactory::class => GeometryXYZTypes\GeometryCollectionImmutable::class,
            GeographyXYZFactories\PointFactory::class => GeographyXYZTypes\PointImmutable::class,
            GeographyXYZFactories\LineStringFactory::class => GeographyXYZTypes\LineStringImmutable::class,
            GeographyXYZFactories\CircularStringFactory::class => GeographyXYZTypes\CircularStringImmutable::class,
            GeographyXYZFactories\CompoundCurveFactory::class => GeographyXYZTypes\CompoundCurveImmutable::class,
            GeographyXYZFactories\PolygonFactory::class => GeographyXYZTypes\PolygonImmutable::class,
            GeographyXYZFactories\TriangleFactory::class => GeographyXYZTypes\TriangleImmutable::class,
            GeographyXYZFactories\MultiPointFactory::class => GeographyXYZTypes\MultiPointImmutable::class,
            GeographyXYZFactories\MultiLineStringFactory::class => GeographyXYZTypes\MultiLineStringImmutable::class,
            GeographyXYZFactories\MultiPolygonFactory::class => GeographyXYZTypes\MultiPolygonImmutable::class,
            GeographyXYZFactories\PolyhedralSurfaceFactory::class => GeographyXYZTypes\PolyhedralSurfaceImmutable::class,
            GeographyXYZFactories\GeographyCollectionFactory::class => GeographyXYZTypes\GeographyCollectionImmutable::class,
        ];
        self::assertCount(22, $factories);
        self::assertSignatures($factories);
    }

    /** Every XYZM public factory mirrors its immutable constructor signature. */
    public function testXyzmCreationSignatures(): void
    {
        $factories = [
            GeometryXYZMFactories\PointFactory::class => GeometryXYZMTypes\PointImmutable::class,
            GeometryXYZMFactories\LineStringFactory::class => GeometryXYZMTypes\LineStringImmutable::class,
            GeometryXYZMFactories\CircularStringFactory::class => GeometryXYZMTypes\CircularStringImmutable::class,
            GeometryXYZMFactories\CompoundCurveFactory::class => GeometryXYZMTypes\CompoundCurveImmutable::class,
            GeometryXYZMFactories\PolygonFactory::class => GeometryXYZMTypes\PolygonImmutable::class,
            GeometryXYZMFactories\TriangleFactory::class => GeometryXYZMTypes\TriangleImmutable::class,
            GeometryXYZMFactories\MultiPointFactory::class => GeometryXYZMTypes\MultiPointImmutable::class,
            GeometryXYZMFactories\MultiLineStringFactory::class => GeometryXYZMTypes\MultiLineStringImmutable::class,
            GeometryXYZMFactories\MultiPolygonFactory::class => GeometryXYZMTypes\MultiPolygonImmutable::class,
            GeometryXYZMFactories\PolyhedralSurfaceFactory::class => GeometryXYZMTypes\PolyhedralSurfaceImmutable::class,
            GeometryXYZMFactories\GeometryCollectionFactory::class => GeometryXYZMTypes\GeometryCollectionImmutable::class,
            GeographyXYZMFactories\PointFactory::class => GeographyXYZMTypes\PointImmutable::class,
            GeographyXYZMFactories\LineStringFactory::class => GeographyXYZMTypes\LineStringImmutable::class,
            GeographyXYZMFactories\CircularStringFactory::class => GeographyXYZMTypes\CircularStringImmutable::class,
            GeographyXYZMFactories\CompoundCurveFactory::class => GeographyXYZMTypes\CompoundCurveImmutable::class,
            GeographyXYZMFactories\PolygonFactory::class => GeographyXYZMTypes\PolygonImmutable::class,
            GeographyXYZMFactories\TriangleFactory::class => GeographyXYZMTypes\TriangleImmutable::class,
            GeographyXYZMFactories\MultiPointFactory::class => GeographyXYZMTypes\MultiPointImmutable::class,
            GeographyXYZMFactories\MultiLineStringFactory::class => GeographyXYZMTypes\MultiLineStringImmutable::class,
            GeographyXYZMFactories\MultiPolygonFactory::class => GeographyXYZMTypes\MultiPolygonImmutable::class,
            GeographyXYZMFactories\PolyhedralSurfaceFactory::class => GeographyXYZMTypes\PolyhedralSurfaceImmutable::class,
            GeographyXYZMFactories\GeographyCollectionFactory::class => GeographyXYZMTypes\GeographyCollectionImmutable::class,
        ];
        self::assertCount(22, $factories);
        self::assertSignatures($factories);
    }
}
