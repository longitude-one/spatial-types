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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use PHPUnit\Framework\TestCase;

/** Public reference-neutral copying in every supported layout and family.
 *
 * {@link https://github.com/longitude-one/spatial-types/issues/77}
 *
 * @internal
 *
 * @coversNothing
 */
final class ImmutableCopyTest extends TestCase
{
    /** EMPTY points and partial nulls retain their exact coordinate values and references. */
    public function testEmptyAndPartiallyNullPointsKeepTheirValues(): void
    {
        $reference = new SpatialReference(0, 'LOCAL');
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(null, null, $reference);
        $partial = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(1, null, 3, null, $reference);

        $emptyCopy = $empty->copy();
        $partialCopy = $partial->copy();

        self::assertNotSame($empty, $emptyCopy);
        self::assertNotSame($partial, $partialCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame([1, null, 3, null], $partialCopy->toArray());
        self::assertSame($reference, $emptyCopy->getSpatialReference());
        self::assertSame($reference, $partialCopy->getSpatialReference());
        self::assertSame([], $empty->toArray());
        self::assertSame([1, null, 3, null], $partial->toArray());
    }

    /** Xy geography copies preserve context and independently copy repeated members. */
    public function testXyGeographyCopiesPointsAndRepeatedMembers(): void
    {
        $pointReference = new SpatialReference(2154, 'LOCAL');
        $lineReference = SpatialReference::epsg(4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(1, 2, $pointReference);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([$point, $point], $lineReference);
        $emptyLine = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable([], $lineReference);

        $pointCopy = $point->copy();
        $lineCopy = $line->copy();
        $emptyCopy = $emptyLine->copy();

        self::assertNotSame($point, $pointCopy);
        self::assertNotSame($line, $lineCopy);
        self::assertNotSame($point, $lineCopy->getPoint(0));
        self::assertNotSame($point, $lineCopy->getPoint(1));
        self::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        self::assertSame([1, 2], $pointCopy->toArray());
        self::assertSame([[1, 2], [1, 2]], $lineCopy->toArray());
        self::assertSame([1, 2], $point->toArray());
        self::assertSame([[1, 2], [1, 2]], $line->toArray());
        self::assertSame($pointReference, $pointCopy->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        self::assertSame($lineReference, $lineCopy->getSpatialReference());
        self::assertSame(CoordinateDimensionEnum::XY, $pointCopy->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $lineCopy->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $pointCopy->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $lineCopy->getFamily());
        self::assertNotSame($emptyLine, $emptyCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame($lineReference, $emptyCopy->getSpatialReference());
        self::assertSame([], $emptyLine->toArray());
    }

    /** Xy geometry copies preserve context and independently copy repeated members. */
    public function testXyGeometryCopiesPointsAndRepeatedMembers(): void
    {
        $pointReference = new SpatialReference(2154, 'LOCAL');
        $lineReference = SpatialReference::epsg(4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable(1, 2, $pointReference);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable([$point, $point], $lineReference);
        $emptyLine = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable([], $lineReference);

        $pointCopy = $point->copy();
        $lineCopy = $line->copy();
        $emptyCopy = $emptyLine->copy();

        self::assertNotSame($point, $pointCopy);
        self::assertNotSame($line, $lineCopy);
        self::assertNotSame($point, $lineCopy->getPoint(0));
        self::assertNotSame($point, $lineCopy->getPoint(1));
        self::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        self::assertSame([1, 2], $pointCopy->toArray());
        self::assertSame([[1, 2], [1, 2]], $lineCopy->toArray());
        self::assertSame([1, 2], $point->toArray());
        self::assertSame([[1, 2], [1, 2]], $line->toArray());
        self::assertSame($pointReference, $pointCopy->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        self::assertSame($lineReference, $lineCopy->getSpatialReference());
        self::assertSame(CoordinateDimensionEnum::XY, $pointCopy->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $lineCopy->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $pointCopy->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $lineCopy->getFamily());
        self::assertNotSame($emptyLine, $emptyCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame($lineReference, $emptyCopy->getSpatialReference());
        self::assertSame([], $emptyLine->toArray());
    }

    /** Xym geography copies preserve context and independently copy repeated members. */
    public function testXymGeographyCopiesPointsAndRepeatedMembers(): void
    {
        $pointReference = new SpatialReference(2154, 'LOCAL');
        $lineReference = SpatialReference::epsg(4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(1, 2, 7, $pointReference);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([$point, $point], $lineReference);
        $emptyLine = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable([], $lineReference);

        $pointCopy = $point->copy();
        $lineCopy = $line->copy();
        $emptyCopy = $emptyLine->copy();

        self::assertNotSame($point, $pointCopy);
        self::assertNotSame($line, $lineCopy);
        self::assertNotSame($point, $lineCopy->getPoint(0));
        self::assertNotSame($point, $lineCopy->getPoint(1));
        self::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        self::assertSame([1, 2, 7], $pointCopy->toArray());
        self::assertSame([[1, 2, 7], [1, 2, 7]], $lineCopy->toArray());
        self::assertSame([1, 2, 7], $point->toArray());
        self::assertSame([[1, 2, 7], [1, 2, 7]], $line->toArray());
        self::assertSame($pointReference, $pointCopy->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        self::assertSame($lineReference, $lineCopy->getSpatialReference());
        self::assertSame(CoordinateDimensionEnum::XYM, $pointCopy->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYM, $lineCopy->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $pointCopy->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $lineCopy->getFamily());
        self::assertNotSame($emptyLine, $emptyCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame($lineReference, $emptyCopy->getSpatialReference());
        self::assertSame([], $emptyLine->toArray());
    }

    /** Xym geometry copies preserve context and independently copy repeated members. */
    public function testXymGeometryCopiesPointsAndRepeatedMembers(): void
    {
        $pointReference = new SpatialReference(2154, 'LOCAL');
        $lineReference = SpatialReference::epsg(4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1, 2, 7, $pointReference);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([$point, $point], $lineReference);
        $emptyLine = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([], $lineReference);

        $pointCopy = $point->copy();
        $lineCopy = $line->copy();
        $emptyCopy = $emptyLine->copy();

        self::assertNotSame($point, $pointCopy);
        self::assertNotSame($line, $lineCopy);
        self::assertNotSame($point, $lineCopy->getPoint(0));
        self::assertNotSame($point, $lineCopy->getPoint(1));
        self::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        self::assertSame([1, 2, 7], $pointCopy->toArray());
        self::assertSame([[1, 2, 7], [1, 2, 7]], $lineCopy->toArray());
        self::assertSame([1, 2, 7], $point->toArray());
        self::assertSame([[1, 2, 7], [1, 2, 7]], $line->toArray());
        self::assertSame($pointReference, $pointCopy->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        self::assertSame($lineReference, $lineCopy->getSpatialReference());
        self::assertSame(CoordinateDimensionEnum::XYM, $pointCopy->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYM, $lineCopy->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $pointCopy->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $lineCopy->getFamily());
        self::assertNotSame($emptyLine, $emptyCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame($lineReference, $emptyCopy->getSpatialReference());
        self::assertSame([], $emptyLine->toArray());
    }

    /** Xyz geography copies preserve context and independently copy repeated members. */
    public function testXyzGeographyCopiesPointsAndRepeatedMembers(): void
    {
        $pointReference = new SpatialReference(2154, 'LOCAL');
        $lineReference = SpatialReference::epsg(4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(1, 2, 3, $pointReference);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([$point, $point], $lineReference);
        $emptyLine = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable([], $lineReference);

        $pointCopy = $point->copy();
        $lineCopy = $line->copy();
        $emptyCopy = $emptyLine->copy();

        self::assertNotSame($point, $pointCopy);
        self::assertNotSame($line, $lineCopy);
        self::assertNotSame($point, $lineCopy->getPoint(0));
        self::assertNotSame($point, $lineCopy->getPoint(1));
        self::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        self::assertSame([1, 2, 3], $pointCopy->toArray());
        self::assertSame([[1, 2, 3], [1, 2, 3]], $lineCopy->toArray());
        self::assertSame([1, 2, 3], $point->toArray());
        self::assertSame([[1, 2, 3], [1, 2, 3]], $line->toArray());
        self::assertSame($pointReference, $pointCopy->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        self::assertSame($lineReference, $lineCopy->getSpatialReference());
        self::assertSame(CoordinateDimensionEnum::XYZ, $pointCopy->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZ, $lineCopy->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $pointCopy->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $lineCopy->getFamily());
        self::assertNotSame($emptyLine, $emptyCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame($lineReference, $emptyCopy->getSpatialReference());
        self::assertSame([], $emptyLine->toArray());
    }

    /** Xyz geometry copies preserve context and independently copy repeated members. */
    public function testXyzGeometryCopiesPointsAndRepeatedMembers(): void
    {
        $pointReference = new SpatialReference(2154, 'LOCAL');
        $lineReference = SpatialReference::epsg(4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1, 2, 3, $pointReference);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([$point, $point], $lineReference);
        $emptyLine = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable([], $lineReference);

        $pointCopy = $point->copy();
        $lineCopy = $line->copy();
        $emptyCopy = $emptyLine->copy();

        self::assertNotSame($point, $pointCopy);
        self::assertNotSame($line, $lineCopy);
        self::assertNotSame($point, $lineCopy->getPoint(0));
        self::assertNotSame($point, $lineCopy->getPoint(1));
        self::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        self::assertSame([1, 2, 3], $pointCopy->toArray());
        self::assertSame([[1, 2, 3], [1, 2, 3]], $lineCopy->toArray());
        self::assertSame([1, 2, 3], $point->toArray());
        self::assertSame([[1, 2, 3], [1, 2, 3]], $line->toArray());
        self::assertSame($pointReference, $pointCopy->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        self::assertSame($lineReference, $lineCopy->getSpatialReference());
        self::assertSame(CoordinateDimensionEnum::XYZ, $pointCopy->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZ, $lineCopy->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $pointCopy->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $lineCopy->getFamily());
        self::assertNotSame($emptyLine, $emptyCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame($lineReference, $emptyCopy->getSpatialReference());
        self::assertSame([], $emptyLine->toArray());
    }

    /** Xyzm geography copies preserve context and independently copy repeated members. */
    public function testXyzmGeographyCopiesPointsAndRepeatedMembers(): void
    {
        $pointReference = new SpatialReference(2154, 'LOCAL');
        $lineReference = SpatialReference::epsg(4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(1, 2, 3, 7, $pointReference);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([$point, $point], $lineReference);
        $emptyLine = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable([], $lineReference);

        $pointCopy = $point->copy();
        $lineCopy = $line->copy();
        $emptyCopy = $emptyLine->copy();

        self::assertNotSame($point, $pointCopy);
        self::assertNotSame($line, $lineCopy);
        self::assertNotSame($point, $lineCopy->getPoint(0));
        self::assertNotSame($point, $lineCopy->getPoint(1));
        self::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        self::assertSame([1, 2, 3, 7], $pointCopy->toArray());
        self::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $lineCopy->toArray());
        self::assertSame([1, 2, 3, 7], $point->toArray());
        self::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $line->toArray());
        self::assertSame($pointReference, $pointCopy->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        self::assertSame($lineReference, $lineCopy->getSpatialReference());
        self::assertSame(CoordinateDimensionEnum::XYZM, $pointCopy->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZM, $lineCopy->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $pointCopy->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $lineCopy->getFamily());
        self::assertNotSame($emptyLine, $emptyCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame($lineReference, $emptyCopy->getSpatialReference());
        self::assertSame([], $emptyLine->toArray());
    }

    /** Xyzm geometry copies preserve context and independently copy repeated members. */
    public function testXyzmGeometryCopiesPointsAndRepeatedMembers(): void
    {
        $pointReference = new SpatialReference(2154, 'LOCAL');
        $lineReference = SpatialReference::epsg(4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1, 2, 3, 7, $pointReference);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([$point, $point], $lineReference);
        $emptyLine = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineStringImmutable([], $lineReference);

        $pointCopy = $point->copy();
        $lineCopy = $line->copy();
        $emptyCopy = $emptyLine->copy();

        self::assertNotSame($point, $pointCopy);
        self::assertNotSame($line, $lineCopy);
        self::assertNotSame($point, $lineCopy->getPoint(0));
        self::assertNotSame($point, $lineCopy->getPoint(1));
        self::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        self::assertSame([1, 2, 3, 7], $pointCopy->toArray());
        self::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $lineCopy->toArray());
        self::assertSame([1, 2, 3, 7], $point->toArray());
        self::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $line->toArray());
        self::assertSame($pointReference, $pointCopy->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        self::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        self::assertSame($lineReference, $lineCopy->getSpatialReference());
        self::assertSame(CoordinateDimensionEnum::XYZM, $pointCopy->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZM, $lineCopy->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $pointCopy->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $lineCopy->getFamily());
        self::assertNotSame($emptyLine, $emptyCopy);
        self::assertSame([], $emptyCopy->toArray());
        self::assertSame($lineReference, $emptyCopy->getSpatialReference());
        self::assertSame([], $emptyLine->toArray());
    }
}
