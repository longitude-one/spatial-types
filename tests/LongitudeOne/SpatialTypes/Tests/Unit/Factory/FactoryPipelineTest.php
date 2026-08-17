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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Factory\Coordinates;
use LongitudeOne\SpatialTypes\Factory\DefaultSpatialFactoryFactory;
use LongitudeOne\SpatialTypes\Factory\FamilyFactories;
use LongitudeOne\SpatialTypes\Factory\Hydrator\CoordinatesHydrator;
use LongitudeOne\SpatialTypes\Factory\Hydrator\SpatialArrayHydrator;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeographicPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPointFactory;
use LongitudeOne\SpatialTypes\Factory\Internal\GeometricPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;
use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialFactoryRegistry;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the configured factory pipeline and its validation boundaries.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\Coordinates
 * @covers \LongitudeOne\SpatialTypes\Factory\DefaultSpatialFactoryFactory
 * @covers \LongitudeOne\SpatialTypes\Factory\FamilyFactories
 * @covers \LongitudeOne\SpatialTypes\Factory\Hydrator\CoordinatesHydrator
 * @covers \LongitudeOne\SpatialTypes\Factory\Hydrator\SpatialArrayHydrator
 * @covers \LongitudeOne\SpatialTypes\Factory\Internal\AbstractPointFactory
 * @covers \LongitudeOne\SpatialTypes\Factory\SpatialFactory
 * @covers \LongitudeOne\SpatialTypes\Factory\SpatialFactoryRegistry
 */
class FactoryPipelineTest extends TestCase
{
    /**
     * Create a complete factory with explicit geometric and geographic registrations.
     */
    private static function createFactory(): SpatialFactory
    {
        $registry = new SpatialFactoryRegistry(self::geographicFactories(), self::geometricFactories());
        $coordinatesHydrator = new CoordinatesHydrator();

        return new SpatialFactory($registry, $coordinatesHydrator, new SpatialArrayHydrator($registry, $coordinatesHydrator));
    }

    /**
     * Create the geographic family constructors.
     */
    private static function geographicFactories(): FamilyFactories
    {
        return new FamilyFactories(
            FamilyEnum::GEOGRAPHY,
            new GeographicPointFactory(),
            new GeographicLineStringFactory(),
            new GeographicPolygonFactory()
        );
    }

    /**
     * Create the geometric family constructors.
     */
    private static function geometricFactories(): FamilyFactories
    {
        return new FamilyFactories(
            FamilyEnum::GEOMETRY,
            new GeometricPointFactory(),
            new GeometricLineStringFactory(),
            new GeometricPolygonFactory()
        );
    }

    /**
     * A factory registry exposes the constructors associated with its configured family.
     */
    public function testConfiguredRegistryExposesItsFactories(): void
    {
        $familyFactories = self::geometricFactories();
        $registry = new SpatialFactoryRegistry($familyFactories);

        static::assertSame($familyFactories->pointFactory, $registry->pointFactory(FamilyEnum::GEOMETRY));
        static::assertSame($familyFactories->lineStringFactory, $registry->lineStringFactory(FamilyEnum::GEOMETRY));
        static::assertSame($familyFactories->polygonFactory, $registry->polygonFactory(FamilyEnum::GEOMETRY));
    }

    /**
     * The coordinate hydrator keeps unsupported optional ordinates absent.
     */
    public function testCoordinatesHydratorLeavesUnsupportedOrdinatesNull(): void
    {
        $hydrator = new CoordinatesHydrator();
        $coordinates = $hydrator->hydrate([1, 2], new SpatialContext());
        $fourDimensionalCoordinates = $hydrator->hydrate(
            [3, 4, 5, 6],
            new SpatialContext(0, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z_M)
        );

        static::assertSame(1, $coordinates->x);
        static::assertSame(2, $coordinates->y);
        static::assertNull($coordinates->z);
        static::assertNull($coordinates->m);
        static::assertSame(5, $fourDimensionalCoordinates->z);
        static::assertSame(6, $fourDimensionalCoordinates->m);
    }

    /**
     * The coordinate hydrator rejects a non-numeric Z or M ordinate before point construction.
     */
    public function testCoordinatesHydratorRejectsNonNumericOptionalOrdinate(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Z coordinate must be a number');

        (new \ReflectionMethod(CoordinatesHydrator::class, 'hydrate'))->invoke(
            new CoordinatesHydrator(),
            [1, 2, 'not-a-number'],
            new SpatialContext(0, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z)
        );
    }

    /**
     * The default builder creates a complete factory and keeps reusing it afterwards.
     */
    public function testDefaultFactoryBuildsAndCachesThePipeline(): void
    {
        $property = new \ReflectionProperty(DefaultSpatialFactoryFactory::class, 'factory');
        $previousFactory = $property->getValue();
        $property->setValue(null, null);

        try {
            $factory = DefaultSpatialFactoryFactory::create();
            $point = $factory->createPointFromIndexedArray([1, 2], new SpatialContext(2154, FamilyEnum::GEOMETRY));

            static::assertSame([1, 2], $point->toArray());
            static::assertSame(2154, $point->getSrid());
            static::assertSame($factory, DefaultSpatialFactoryFactory::create());
        } finally {
            $property->setValue(null, $previousFactory);
        }
    }

    /**
     * @param \Closure $operation factory operation that violates a public input contract
     * @param string   $message   expected message fragment
     */
    #[DataProvider('provideInvalidFactoryOperations')]
    public function testFactoryRejectsInvalidTypedInput(\Closure $operation, string $message): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains($message);

        $operation(self::createFactory());
    }

    /**
     * @return \Generator<string, array{0: \Closure, 1: string}, null, void>
     */
    public static function provideInvalidFactoryOperations(): \Generator
    {
        yield 'line string contains an unsupported typed value' => [
            static function (SpatialFactory $factory): void {
                (new \ReflectionMethod($factory, 'createLineString'))->invoke($factory, [new \stdClass()], new SpatialContext());
            },
            'objects implementing PointInterface',
        ];

        yield 'polygon contains an unsupported typed value' => [
            static function (SpatialFactory $factory): void {
                (new \ReflectionMethod($factory, 'createPolygon'))->invoke($factory, [new \stdClass()], new SpatialContext());
            },
            'objects implementing LineStringInterface',
        ];
    }

    /**
     * A point factory requires every ordinate declared by its dimension.
     */
    public function testPointFactoryRejectsMissingRequiredOrdinate(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('third coordinate is missing');

        (new GeometricPointFactory())->create(
            new Coordinates(1, 2),
            new SpatialContext(0, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z)
        );
    }

    /**
     * A registry rejects duplicate family registrations.
     */
    public function testRegistryRejectsDuplicateFamilyRegistration(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('already registered for the Geometry family');

        new SpatialFactoryRegistry(self::geometricFactories(), self::geometricFactories());
    }

    /**
     * A registry makes absent family registrations explicit.
     */
    public function testRegistryRejectsUnregisteredFamilyLookup(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('No factories are registered for the Geography family');

        (new SpatialFactoryRegistry(self::geometricFactories()))->pointFactory(FamilyEnum::GEOGRAPHY);
    }

    /**
     * The factory creates line strings, points, and polygons from their typed components.
     */
    public function testSpatialFactoryCreatesTypedComponents(): void
    {
        $factory = self::createFactory();
        $context = new SpatialContext(2154, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);
        $firstPoint = new Point(0, 0, 2154);
        $secondPoint = new Point(1, 1, 2154);
        $ring = new LineString([$firstPoint, $secondPoint, new Point(0, 1, 2154), $firstPoint], 2154);

        $point = $factory->createPoint(new Coordinates(2, 3), $context);
        $lineString = $factory->createLineString([$firstPoint, $secondPoint], $context);
        $polygon = $factory->createPolygon([$ring], $context);

        static::assertSame([2, 3], $point->toArray());
        static::assertSame([[0, 0], [1, 1]], $lineString->toArray());
        static::assertSame([[[0, 0], [1, 1], [0, 1], [0, 0]]], $polygon->toArray());
        static::assertSame(2154, $point->getSrid());
        static::assertSame(2154, $lineString->getSrid());
        static::assertSame(2154, $polygon->getSrid());
    }

    /**
     * A typed coordinate value must be compatible with the requested context dimension.
     */
    public function testSpatialFactoryRejectsIncompatibleCoordinateDimension(): void
    {
        self::expectException(InvalidDimensionException::class);

        self::createFactory()->createPoint(
            new Coordinates(1, 2, 3),
            new SpatialContext(0, FamilyEnum::GEOMETRY, DimensionEnum::X_Y)
        );
    }
}
