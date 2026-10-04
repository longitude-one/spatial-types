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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types\Dimension3z\Geometry;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Interfaces\CircularStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\CurveInterface;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * Public circular-string construction in XYZ Geometry.
 *
 * @internal
 *
 * @coversNothing
 */
class CircularStringTest extends TestCase
{
    /** Empty values retain their declared layout and reference. */
    public function testEmpty(): void
    {
        $curve = new CircularString([], 4326);

        static::assertTrue($curve->isEmpty());
        static::assertSame([], $curve->getPoints());
        static::assertSame([], $curve->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZ, $curve->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $curve->getFamily());
        static::assertSame(4326, $curve->getSrid());
    }

    /** Immutable replacement preserves this concrete layout and family. */
    public function testReplacePoint(): void
    {
        $curve = new CircularString([[0, 0, 3], [1, 1, 4], [2, 0, 5]], 4326);
        $replacement = $curve->withPoint(1, Coordinates::xyz(1, 2, 8));

        static::assertSame(CircularString::class, $replacement::class);
        static::assertSame([[0, 0, 3], [1, 1, 4], [2, 0, 5]], $curve->toArray());
        static::assertSame([[0, 0, 3], [1, 2, 8], [2, 0, 5]], $replacement->toArray());
        static::assertSame(4326, $replacement->getPoint(1)->getSrid());
        static::assertNotSame($curve->getPoint(0), $replacement->getPoint(0));
    }

    /** Three defining points retain circular interpolation and their ordinates. */
    public function testSingleArc(): void
    {
        $curve = new CircularString([[0, 0, 3], [1, 1, 4], [2, 0, 5]], 4326);

        static::assertContains(CurveInterface::class, class_implements($curve));
        static::assertContains(CircularStringInterface::class, class_implements($curve));
        static::assertFalse($curve->isEmpty());
        static::assertSame(GeometryTypeEnum::CIRCULARSTRING, $curve->getType());
        static::assertSame(CoordinateDimensionEnum::XYZ, $curve->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $curve->getFamily());
        static::assertSame([[0, 0, 3], [1, 1, 4], [2, 0, 5]], $curve->toArray());
        static::assertCount(3, $curve->getPoints());
        static::assertSame($curve->getPoints(), $curve->getElements());
        static::assertInstanceOf(Point::class, $curve->getPoint(0));
        static::assertSame(4326, $curve->getPoint(-1)->getSrid());
        static::assertSame($curve->getPoint(2), $curve->getPoint(-1));
        static::assertSame($curve->getPoint(0), $curve->getPoint(3));
    }
}
