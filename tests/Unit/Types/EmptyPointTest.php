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
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as GeometryPoint;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Empty point tests.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPoint
 */
class EmptyPointTest extends TestCase
{
    /**
     * @param object $point Configured point instance
     *
     * @throws \LogicException when a configured concrete class does not implement PointInterface
     */
    private static function asPoint(object $point): PointInterface
    {
        if (!$point instanceof PointInterface) {
            throw new \LogicException('Configured point class does not implement PointInterface.');
        }

        return $point;
    }

    /**
     * @param PointInterface $point Configured point
     *
     * @throws \LogicException when a configured concrete class does not expose a known dimension
     */
    private static function dimensionFromPoint(PointInterface $point): CoordinateDimensionEnum
    {
        return match ([$point->hasZ(), $point->hasM()]) {
            [false, false] => CoordinateDimensionEnum::XY,
            [false, true] => CoordinateDimensionEnum::XYM,
            [true, false] => CoordinateDimensionEnum::XYZ,
            [true, true] => CoordinateDimensionEnum::XYZM,
        };
    }

    /**
     * @param CoordinateDimensionEnum $dimension Coordinate dimension
     */
    private static function dimensionNamespace(CoordinateDimensionEnum $dimension): string
    {
        return match ($dimension) {
            CoordinateDimensionEnum::XY => 'Dimension2',
            CoordinateDimensionEnum::XYM => 'Dimension3m',
            CoordinateDimensionEnum::XYZ => 'Dimension3z',
            CoordinateDimensionEnum::XYZM => 'Dimension4zm',
        };
    }

    /**
     * @return array<string, array{0: SpatialModelEnum, 1: CoordinateDimensionEnum}>
     */
    private static function familiesAndDimensions(): array
    {
        $values = [];
        foreach (SpatialModelEnum::cases() as $family) {
            foreach (CoordinateDimensionEnum::cases() as $dimension) {
                $values[$family->value.' '.$dimension->value] = [$family, $dimension];
            }
        }

        return $values;
    }

    /**
     * Empty points preserve their dimension, family, and spatial reference.
     *
     * @param PointInterface $point Empty point
     */
    #[DataProvider('provideEmptyPoints')]
    public function testEmptyPointCanBeCreated(PointInterface $point): void
    {
        static::assertTrue($point->isEmpty());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertNull($point->getLongitude());
        static::assertNull($point->getLatitude());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame([], $point->toArray());

        if ($point->hasZ()) {
            static::assertNull($point->getZ());
        }

        if ($point->hasM()) {
            static::assertNull($point->getM());
        }
    }

    /**
     * @return \Generator<string, array{0: PointInterface}>
     */
    public static function provideEmptyPoints(): \Generator
    {
        foreach (self::familiesAndDimensions() as $name => [$family, $dimension]) {
            $familyNamespace = SpatialModelEnum::GEOMETRY === $family ? 'Geometry' : 'Geography';
            $class = sprintf('LongitudeOne\SpatialTypes\Types\%s\%s\Point', self::dimensionNamespace($dimension), $familyNamespace);

            yield $name => [self::asPoint(new $class(srid: 4326))];
        }
    }

    /**
     * The indexed-array factory accepts an empty coordinate array.
     *
     * @param SpatialModelEnum        $family    Point family
     * @param CoordinateDimensionEnum $dimension Point dimension
     */
    #[DataProvider('provideFamiliesAndDimensions')]
    public function testFactoryCreatesAnEmptyPoint(SpatialModelEnum $family, CoordinateDimensionEnum $dimension): void
    {
        $point = FromIndexedArrayFactory::createPoint([], 4326, $family, $dimension);

        static::assertTrue($point->isEmpty());
        static::assertSame([], $point->toArray());
        static::assertSame(4326, $point->getSrid());
    }

    /**
     * @return \Generator<string, array{0: SpatialModelEnum, 1: CoordinateDimensionEnum}>
     */
    public static function provideFamiliesAndDimensions(): \Generator
    {
        yield from self::familiesAndDimensions();
    }

    /**
     * Empty points cannot be a component of a line string.
     */
    public function testLineStringRejectsAnEmptyPoint(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('cannot contain an empty point');

        new LineString([new GeometryPoint(), new GeometryPoint(), new GeometryPoint(), new GeometryPoint()]);
    }

    /**
     * Point constructors reject an incomplete coordinate tuple.
     */
    public function testPointConstructorRejectsPartialCoordinates(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('All point coordinates must be provided');

        new GeometryPoint(null, 2);
    }

    /**
     * A coordinate tuple produces a non-empty copy of an empty point.
     *
     * @param PointInterface $point       Empty point
     * @param Coordinates    $coordinates Replacement coordinates
     */
    #[DataProvider('provideEmptyPointsAndCoordinates')]
    public function testWithCoordinatesCreatesNonEmptyPoint(PointInterface $point, Coordinates $coordinates): void
    {
        $replacement = $point->withCoordinates($coordinates);

        static::assertTrue($point->isEmpty());
        static::assertFalse($replacement->isEmpty());
        static::assertEquals($coordinates, $replacement->getCoordinates());
    }

    /**
     * @return \Generator<string, array{0: PointInterface, 1: Coordinates}>
     */
    public static function provideEmptyPointsAndCoordinates(): \Generator
    {
        foreach (self::provideEmptyPoints() as $name => [$point]) {
            $coordinates = match (self::dimensionFromPoint($point)) {
                CoordinateDimensionEnum::XY => Coordinates::xy(1, 2),
                CoordinateDimensionEnum::XYM => Coordinates::xym(1, 2, 3),
                CoordinateDimensionEnum::XYZ => Coordinates::xyz(1, 2, 3),
                CoordinateDimensionEnum::XYZM => Coordinates::xyzm(1, 2, 3, 4),
            };

            yield $name => [$point, $coordinates];
        }
    }
}
