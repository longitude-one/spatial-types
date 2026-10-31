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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use PHPUnit\Framework\TestCase;

/** Exact two-point line classification from ISO/IEC CD 13249-3, clause 4.2.5.
 * @internal
 *
 * @coversNothing
 */
final class LineClassificationTest extends TestCase
{
    /** Reclassifying an immutable replacement leaves the original path intact. */
    public function testImmutableReplacementPreservesOriginal(): void
    {
        $path = new LineStringImmutable([[0, 0], [1, 1], [2, 2]], 4326);
        $line = $path->withArrayOfCoordinates([[3, 3], [4, 4]]);

        self::assertFalse($path->isLine());
        self::assertTrue($line->isLine());
        self::assertNotSame($path, $line);
        self::assertSame([[0, 0], [1, 1], [2, 2]], $path->toArray());
        self::assertSame([[3, 3], [4, 4]], $line->toArray());
        self::assertSame(4326, $line->getSrid());
    }

    /** Xy geography immutable classification preserves the path. */
    public function testXyGeographyImmutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([[0, 0]], 4326);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([[0, 0], [1, 1]], 4326);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([[0, 0], [1, 1], [2, 2]], 4326);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([[0, 0], [1, 1], [0, 0]], 4326);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0], [1, 1], [2, 2]], $path->toArray());
        self::assertSame([[0, 0], [1, 1], [0, 0]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xy geography mutable classification preserves the path. */
    public function testXyGeographyMutableClassification(): void
    {
        $empty = new LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $single = new LineString([[0, 0]], 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new LineString([[0, 0], [1, 1]], 4326, SpatialModelEnum::GEOGRAPHY);
        $path = new LineString([[0, 0], [1, 1], [2, 2]], 4326, SpatialModelEnum::GEOGRAPHY);
        $closed = new LineString([[0, 0], [1, 1], [0, 0]], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0], [1, 1], [2, 2]], $path->toArray());
        self::assertSame([[0, 0], [1, 1], [0, 0]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xy geometry immutable classification preserves the path. */
    public function testXyGeometryImmutableClassification(): void
    {
        $empty = new LineStringImmutable([], 4326);
        $single = new LineStringImmutable([[0, 0]], 4326);
        $line = new LineStringImmutable([[0, 0], [1, 1]], 4326);
        $path = new LineStringImmutable([[0, 0], [1, 1], [2, 2]], 4326);
        $closed = new LineStringImmutable([[0, 0], [1, 1], [0, 0]], 4326);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0], [1, 1], [2, 2]], $path->toArray());
        self::assertSame([[0, 0], [1, 1], [0, 0]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xy geometry mutable classification preserves the path. */
    public function testXyGeometryMutableClassification(): void
    {
        $empty = new LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $single = new LineString([[0, 0]], 4326, SpatialModelEnum::GEOMETRY);
        $line = new LineString([[0, 0], [1, 1]], 4326, SpatialModelEnum::GEOMETRY);
        $path = new LineString([[0, 0], [1, 1], [2, 2]], 4326, SpatialModelEnum::GEOMETRY);
        $closed = new LineString([[0, 0], [1, 1], [0, 0]], 4326, SpatialModelEnum::GEOMETRY);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0], [1, 1], [2, 2]], $path->toArray());
        self::assertSame([[0, 0], [1, 1], [0, 0]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xym geography immutable classification preserves the path. */
    public function testXymGeographyImmutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([[0, 0, 3]], 4326);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([[0, 0, 3], [1, 1, 4]], 4326);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([[0, 0, 3], [1, 1, 4], [2, 2, 5]], 4326);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([[0, 0, 3], [1, 1, 4], [0, 0, 3]], 4326);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3], [1, 1, 4], [2, 2, 5]], $path->toArray());
        self::assertSame([[0, 0, 3], [1, 1, 4], [0, 0, 3]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xym geography mutable classification preserves the path. */
    public function testXymGeographyMutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 3]], 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 3], [1, 1, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 3], [1, 1, 4], [2, 2, 5]], 4326, SpatialModelEnum::GEOGRAPHY);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 3], [1, 1, 4], [0, 0, 3]], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3], [1, 1, 4], [2, 2, 5]], $path->toArray());
        self::assertSame([[0, 0, 3], [1, 1, 4], [0, 0, 3]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xym geometry immutable classification preserves the path. */
    public function testXymGeometryImmutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([[0, 0, 3]], 4326);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([[0, 0, 3], [1, 1, 4]], 4326);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([[0, 0, 3], [1, 1, 4], [2, 2, 5]], 4326);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([[0, 0, 3], [1, 1, 4], [0, 0, 3]], 4326);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3], [1, 1, 4], [2, 2, 5]], $path->toArray());
        self::assertSame([[0, 0, 3], [1, 1, 4], [0, 0, 3]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xym geometry mutable classification preserves the path. */
    public function testXymGeometryMutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 3]], 4326, SpatialModelEnum::GEOMETRY);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 3], [1, 1, 4]], 4326, SpatialModelEnum::GEOMETRY);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 3], [1, 1, 4], [2, 2, 5]], 4326, SpatialModelEnum::GEOMETRY);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, 0, 3], [1, 1, 4], [0, 0, 3]], 4326, SpatialModelEnum::GEOMETRY);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3], [1, 1, 4], [2, 2, 5]], $path->toArray());
        self::assertSame([[0, 0, 3], [1, 1, 4], [0, 0, 3]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xyz geography immutable classification preserves the path. */
    public function testXyzGeographyImmutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([[0, 0, 3]], 4326);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([[0, 0, 3], [1, 1, 4]], 4326);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([[0, 0, 3], [1, 1, 4], [2, 2, 5]], 4326);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([[0, 0, 3], [1, 1, 4], [0, 0, 3]], 4326);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3], [1, 1, 4], [2, 2, 5]], $path->toArray());
        self::assertSame([[0, 0, 3], [1, 1, 4], [0, 0, 3]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xyz geography mutable classification preserves the path. */
    public function testXyzGeographyMutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 3]], 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 3], [1, 1, 4]], 4326, SpatialModelEnum::GEOGRAPHY);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 3], [1, 1, 4], [2, 2, 5]], 4326, SpatialModelEnum::GEOGRAPHY);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 3], [1, 1, 4], [0, 0, 3]], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3], [1, 1, 4], [2, 2, 5]], $path->toArray());
        self::assertSame([[0, 0, 3], [1, 1, 4], [0, 0, 3]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xyz geometry immutable classification preserves the path. */
    public function testXyzGeometryImmutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([[0, 0, 3]], 4326);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([[0, 0, 3], [1, 1, 4]], 4326);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([[0, 0, 3], [1, 1, 4], [2, 2, 5]], 4326);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([[0, 0, 3], [1, 1, 4], [0, 0, 3]], 4326);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3], [1, 1, 4], [2, 2, 5]], $path->toArray());
        self::assertSame([[0, 0, 3], [1, 1, 4], [0, 0, 3]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xyz geometry mutable classification preserves the path. */
    public function testXyzGeometryMutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 3]], 4326, SpatialModelEnum::GEOMETRY);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 3], [1, 1, 4]], 4326, SpatialModelEnum::GEOMETRY);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 3], [1, 1, 4], [2, 2, 5]], 4326, SpatialModelEnum::GEOMETRY);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, 0, 3], [1, 1, 4], [0, 0, 3]], 4326, SpatialModelEnum::GEOMETRY);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3], [1, 1, 4], [2, 2, 5]], $path->toArray());
        self::assertSame([[0, 0, 3], [1, 1, 4], [0, 0, 3]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xyzm geography immutable classification preserves the path. */
    public function testXyzmGeographyImmutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([[0, 0, 3, 7]], 4326);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([[0, 0, 3, 7], [1, 1, 4, 8]], 4326);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([[0, 0, 3, 7], [1, 1, 4, 8], [2, 2, 5, 9]], 4326);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([[0, 0, 3, 7], [1, 1, 4, 8], [0, 0, 3, 7]], 4326);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3, 7], [1, 1, 4, 8], [2, 2, 5, 9]], $path->toArray());
        self::assertSame([[0, 0, 3, 7], [1, 1, 4, 8], [0, 0, 3, 7]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xyzm geography mutable classification preserves the path. */
    public function testXyzmGeographyMutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([], 4326, SpatialModelEnum::GEOGRAPHY);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 3, 7]], 4326, SpatialModelEnum::GEOGRAPHY);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 3, 7], [1, 1, 4, 8]], 4326, SpatialModelEnum::GEOGRAPHY);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 3, 7], [1, 1, 4, 8], [2, 2, 5, 9]], 4326, SpatialModelEnum::GEOGRAPHY);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 3, 7], [1, 1, 4, 8], [0, 0, 3, 7]], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3, 7], [1, 1, 4, 8], [2, 2, 5, 9]], $path->toArray());
        self::assertSame([[0, 0, 3, 7], [1, 1, 4, 8], [0, 0, 3, 7]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xyzm geometry immutable classification preserves the path. */
    public function testXyzmGeometryImmutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([[0, 0, 3, 7]], 4326);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([[0, 0, 3, 7], [1, 1, 4, 8]], 4326);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([[0, 0, 3, 7], [1, 1, 4, 8], [2, 2, 5, 9]], 4326);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([[0, 0, 3, 7], [1, 1, 4, 8], [0, 0, 3, 7]], 4326);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3, 7], [1, 1, 4, 8], [2, 2, 5, 9]], $path->toArray());
        self::assertSame([[0, 0, 3, 7], [1, 1, 4, 8], [0, 0, 3, 7]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }

    /** Xyzm geometry mutable classification preserves the path. */
    public function testXyzmGeometryMutableClassification(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([], 4326, SpatialModelEnum::GEOMETRY);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 3, 7]], 4326, SpatialModelEnum::GEOMETRY);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 3, 7], [1, 1, 4, 8]], 4326, SpatialModelEnum::GEOMETRY);
        $path = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 3, 7], [1, 1, 4, 8], [2, 2, 5, 9]], 4326, SpatialModelEnum::GEOMETRY);
        $closed = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, 0, 3, 7], [1, 1, 4, 8], [0, 0, 3, 7]], 4326, SpatialModelEnum::GEOMETRY);

        self::assertFalse($empty->isLine());
        self::assertFalse($single->isLine());
        self::assertTrue($line->isLine());
        self::assertFalse($path->isLine());
        self::assertFalse($closed->isLine());
        self::assertSame([[0, 0, 3, 7], [1, 1, 4, 8], [2, 2, 5, 9]], $path->toArray());
        self::assertSame([[0, 0, 3, 7], [1, 1, 4, 8], [0, 0, 3, 7]], $closed->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $path->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $path->getFamily());
        self::assertSame(4326, $path->getSrid());
    }
}
