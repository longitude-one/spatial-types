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

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use PHPUnit\Framework\TestCase;

/** Approved R2 corrections without deciding unresolved R3/R4 contracts.
 * @internal
 *
 * @coversNothing
 */
final class RepeatedMultiPointInstanceTest extends TestCase
{
    /** Partial and EMPTY identity behavior remains outside the approved correction. */
    public function testUnresolvedPositionSemanticsRemainUnchanged(): void
    {
        $empty = new PointImmutable();
        $partial = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1, 2, null);
        $emptyMembers = new MultiPointImmutable([$empty, $empty]);
        $partialMembers = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$partial, $partial]);

        self::assertSame([[], []], $emptyMembers->toArray());
        self::assertSame([[1, 2, null], [1, 2, null]], $partialMembers->toArray());
    }

    /** Shared Xy geography immutable positions are non-simple. */
    public function testXyGeographyImmutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(1, 2, 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable([$point, $point], 4326);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2], [1, 2]], $multiPoint->toArray());
        self::assertSame([1, 2], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xy geography mutable positions are non-simple. */
    public function testXyGeographyMutableRepeatedInstance(): void
    {
        $point = new Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);
        $multiPoint = new MultiPoint([$point, $point], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2], [1, 2]], $multiPoint->toArray());
        self::assertSame([1, 2], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xy geometry immutable positions are non-simple. */
    public function testXyGeometryImmutableRepeatedInstance(): void
    {
        $point = new PointImmutable(1, 2, 4326);
        $multiPoint = new MultiPointImmutable([$point, $point], 4326);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2], [1, 2]], $multiPoint->toArray());
        self::assertSame([1, 2], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xy geometry mutable positions are non-simple. */
    public function testXyGeometryMutableRepeatedInstance(): void
    {
        $point = new Point(1, 2, 4326, SpatialModelEnum::GEOMETRY);
        $multiPoint = new MultiPoint([$point, $point], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2], [1, 2]], $multiPoint->toArray());
        self::assertSame([1, 2], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xym geography immutable positions are non-simple. */
    public function testXymGeographyImmutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(1, 2, 7, 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([$point, $point], 4326);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 7], [1, 2, 7]], $multiPoint->toArray());
        self::assertSame([1, 2, 7], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xym geography mutable positions are non-simple. */
    public function testXymGeographyMutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1, 2, 7, 4326, SpatialModelEnum::GEOGRAPHY);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$point, $point], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 7], [1, 2, 7]], $multiPoint->toArray());
        self::assertSame([1, 2, 7], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xym geometry immutable positions are non-simple. */
    public function testXymGeometryImmutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1, 2, 7, 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$point, $point], 4326);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 7], [1, 2, 7]], $multiPoint->toArray());
        self::assertSame([1, 2, 7], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xym geometry mutable positions are non-simple. */
    public function testXymGeometryMutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1, 2, 7, 4326, SpatialModelEnum::GEOMETRY);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([$point, $point], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 7], [1, 2, 7]], $multiPoint->toArray());
        self::assertSame([1, 2, 7], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xyz geography immutable positions are non-simple. */
    public function testXyzGeographyImmutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(1, 2, 3, 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([$point, $point], 4326);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 3], [1, 2, 3]], $multiPoint->toArray());
        self::assertSame([1, 2, 3], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xyz geography mutable positions are non-simple. */
    public function testXyzGeographyMutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(1, 2, 3, 4326, SpatialModelEnum::GEOGRAPHY);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$point, $point], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 3], [1, 2, 3]], $multiPoint->toArray());
        self::assertSame([1, 2, 3], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xyz geometry immutable positions are non-simple. */
    public function testXyzGeometryImmutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1, 2, 3, 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([$point, $point], 4326);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 3], [1, 2, 3]], $multiPoint->toArray());
        self::assertSame([1, 2, 3], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xyz geometry mutable positions are non-simple. */
    public function testXyzGeometryMutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(1, 2, 3, 4326, SpatialModelEnum::GEOMETRY);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([$point, $point], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 3], [1, 2, 3]], $multiPoint->toArray());
        self::assertSame([1, 2, 3], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xyzm geography immutable positions are non-simple. */
    public function testXyzmGeographyImmutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(1, 2, 3, 7, 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([$point, $point], 4326);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $multiPoint->toArray());
        self::assertSame([1, 2, 3, 7], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xyzm geography mutable positions are non-simple. */
    public function testXyzmGeographyMutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(1, 2, 3, 7, 4326, SpatialModelEnum::GEOGRAPHY);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$point, $point], 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $multiPoint->toArray());
        self::assertSame([1, 2, 3, 7], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xyzm geometry immutable positions are non-simple. */
    public function testXyzmGeometryImmutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1, 2, 3, 7, 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([$point, $point], 4326);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $multiPoint->toArray());
        self::assertSame([1, 2, 3, 7], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }

    /** Shared Xyzm geometry mutable positions are non-simple. */
    public function testXyzmGeometryMutableRepeatedInstance(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(1, 2, 3, 7, 4326, SpatialModelEnum::GEOMETRY);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([$point, $point], 4326, SpatialModelEnum::GEOMETRY);

        self::assertSame($point, $multiPoint->getPoints()[0]);
        self::assertSame($point, $multiPoint->getPoints()[1]);
        self::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $multiPoint->toArray());
        self::assertSame([1, 2, 3, 7], $point->toArray());
        self::assertSame(4326, $multiPoint->getSrid());
    }
}
