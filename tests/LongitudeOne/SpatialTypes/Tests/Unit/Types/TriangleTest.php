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

use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\PolygonInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\AbstractTriangle;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Triangle;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineString;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractTriangle
 */
class TriangleTest extends TestCase
{
    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testConstructionAndCopies(string $class, array $ring): void
    {
        $triangle = new $class([$ring], 4326);
        static::assertInstanceOf(PolygonInterface::class, $triangle);
        static::assertSame(GeometryTypeEnum::TRIANGLE, $triangle->getType());
        static::assertSame([$ring], $triangle->toArray());
        static::assertSame('Triangle', $triangle->jsonSerialize()['type']);
        static::assertFalse($triangle->isEmpty());
        static::assertSame(str_contains($class, '3z') || str_contains($class, '4zm'), $triangle->hasZ());
        static::assertSame(str_contains($class, '3m') || str_contains($class, '4zm'), $triangle->hasM());
        static::assertSame($triangle->getFamily(), $triangle->getRing(0)->getFamily());
        static::assertTrue($triangle->hasSameDimension($triangle->getRing(0)));
        static::assertSame([$ring], (new $class([$triangle->getRing(0)], 4326))->toArray());
        static::assertSame([$ring], (new $class([$triangle->getRing(0)->getPoints()], 4326))->toArray());

        $copies = [$triangle->withRing(-1, $ring), $triangle->withArrayOfCoordinates([$ring]), $triangle->withSrid(3857), $triangle->withSpatialReference(SpatialReference::epsg(4326))];
        foreach ($copies as $copy) {
            static::assertSame($class, $copy::class);
            static::assertSame($triangle->getFamily(), $copy->getFamily());
            static::assertTrue($triangle->hasSameDimension($copy));
            static::assertSame([$ring], $copy->toArray());
            static::assertNotSame($triangle->getRing(0), $copy->getRing(0));
            static::assertNotSame($triangle->getRing(0)->getPoint(0), $copy->getRing(0)->getPoint(0));
            static::assertSame($copy->getSrid(), $copy->getRing(0)->getPoint(0)->getSrid());
        }
        static::assertSame(4326, $triangle->getSrid());
        static::assertSame(3857, $copies[2]->getSrid());
        static::assertSame('EPSG', $copies[3]->getRing(0)->getPoint(0)->getSpatialReference()->authority);
        static::assertTrue((new $class([]))->isEmpty());
        static::assertTrue($triangle->withArrayOfCoordinates([])->isEmpty());
        static::assertSame([$ring], $triangle->toArray());
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testPointReplacementPreservesClosure(string $class, array $ring): void
    {
        $triangle = new $class([$ring], 4326);
        $point = $triangle->getRing(0)->getPoint(0);
        $replacementRing = $ring;
        $replacementRing[0][0] = -1;
        $replacementRing[3] = $replacementRing[0];
        $replacement = new $class([$replacementRing], 4326);
        $coordinates = $replacement->getRing(0)->getPoint(0)->getCoordinates();
        static::assertInstanceOf(Coordinates::class, $coordinates);
        foreach ([0, -1] as $index) {
            $copy = $triangle->withPoint(0, $index, $coordinates);
            static::assertSame($class, $copy::class);
            static::assertSame([$replacementRing], $copy->toArray());
            static::assertSame([$ring], $triangle->toArray());
            static::assertSame(4326, $copy->getSrid());
            static::assertNotSame($point, $copy->getRing(0)->getPoint(0));
            static::assertSame($copy->getRing(0)->getPoint(0)->toArray(), $copy->getRing(0)->getPoint(-1)->toArray());
        }
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testRejectsFourthPointDifferentFromFirst(string $class, array $ring): void
    {
        $ring[3][0] = 1;
        static::assertCount(4, $ring);
        static::assertNotSame($ring[0], $ring[3]);

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('A linear ring must have equal first and last points.');
        new $class([$ring]);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testRejectsFourVertices(string $class, array $ring): void
    {
        $ring[3] = $ring[2];
        $ring[3][0] = 1;
        $ring[] = $ring[0];
        $this->expectException(InvalidValueException::class);
        new $class([$ring]);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testRejectsHoles(string $class, array $ring): void
    {
        $this->expectException(InvalidValueException::class);
        new $class([$ring, $ring]);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testRejectsInvalidCoordinates(string $class, array $ring): void
    {
        $ring[1][0] = 'invalid-coordinate';
        $this->expectException(InvalidValueException::class);
        new $class([$ring]);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testRejectsMixedDimension(string $class, array $ring): void
    {
        $other = 2 === count($ring[0])
            ? new LineString([[0, 0, 0], [4, 0, 0], [0, 4, 0], [0, 0, 0]])
            : new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString([[0, 0], [4, 0], [0, 4], [0, 0]]);
        if (str_contains($class, 'Geography')) {
            $otherClass = str_replace('Geometry', 'Geography', $other::class);
            $other = new $otherClass($other->toArray());
        }
        $this->expectException(InvalidDimensionException::class);
        new $class([$other]);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testRejectsMixedFamily(string $class, array $ring): void
    {
        $otherClass = str_contains($class, 'Geography') ? str_replace('Geography', 'Geometry', $class) : str_replace('Geometry', 'Geography', $class);
        $other = new $otherClass([$ring]);
        static::assertInstanceOf(AbstractTriangle::class, $other);
        $this->expectException(InvalidFamilyException::class);
        new $class([$other->getRing(0)]);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testRejectsMixedSrid(string $class, array $ring): void
    {
        $triangle = new $class([$ring], 4326);
        $this->expectException(InvalidSridException::class);
        new $class([$triangle->getRing(0)], 3857);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testReplacementRejectsDuplicateVertices(string $class, array $ring): void
    {
        $triangle = new $class([$ring]);
        $coordinates = $triangle->getRing(0)->getPoint(1)->getCoordinates();
        static::assertInstanceOf(Coordinates::class, $coordinates);
        $this->expectException(InvalidValueException::class);
        $triangle->withPoint(0, 0, $coordinates);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testReplacementRejectsHoles(string $class, array $ring): void
    {
        $triangle = new $class([$ring]);
        $this->expectException(InvalidValueException::class);
        $triangle->withArrayOfCoordinates([$ring, $ring]);
    }

    /**
     * @param string                                        $class Concrete type
     * @param list<array{0: int, 1: int, 2?: int, 3?: int}> $ring  Exterior coordinates
     *
     * @phpstan-param class-string<AbstractTriangle> $class
     */
    #[DataProvider('triangles')]
    public function testReplacementRejectsWrongPointCount(string $class, array $ring): void
    {
        $triangle = new $class([$ring]);
        $this->expectException(InvalidValueException::class);
        $triangle->withRing(0, array_slice($ring, 0, 3));
    }

    /** @return iterable<string, array{class-string<AbstractTriangle>, list<array{0: int, 1: int, 2?: int, 3?: int}>}> */
    public static function triangles(): iterable
    {
        yield 'Dimension2 Geometry' => [Triangle::class, [[0, 0], [4, 0], [0, 4], [0, 0]]];

        yield 'Dimension2 Geography' => [\LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Triangle::class, [[0, 0], [4, 0], [0, 4], [0, 0]]];

        yield 'Dimension3m Geometry' => [\LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Triangle::class, [[0, 0, 7], [4, 0, 7], [0, 4, 7], [0, 0, 7]]];

        yield 'Dimension3m Geography' => [\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Triangle::class, [[0, 0, 7], [4, 0, 7], [0, 4, 7], [0, 0, 7]]];

        yield 'Dimension3z Geometry' => [\LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Triangle::class, [[0, 0, 5], [4, 0, 5], [0, 4, 5], [0, 0, 5]]];

        yield 'Dimension3z Geography' => [\LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Triangle::class, [[0, 0, 5], [4, 0, 5], [0, 4, 5], [0, 0, 5]]];

        yield 'Dimension4zm Geometry' => [\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Triangle::class, [[0, 0, 5, 7], [4, 0, 5, 7], [0, 4, 5, 7], [0, 0, 5, 7]]];

        yield 'Dimension4zm Geography' => [\LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Triangle::class, [[0, 0, 5, 7], [4, 0, 5, 7], [0, 4, 5, 7], [0, 0, 5, 7]]];
    }
}
