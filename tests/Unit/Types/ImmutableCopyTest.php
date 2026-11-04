<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

        static::assertNotSame($empty, $emptyCopy);
        static::assertNotSame($partial, $partialCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame([1, null, 3, null], $partialCopy->toArray());
        static::assertSame($reference, $emptyCopy->getSpatialReference());
        static::assertSame($reference, $partialCopy->getSpatialReference());
        static::assertSame([], $empty->toArray());
        static::assertSame([1, null, 3, null], $partial->toArray());
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

        static::assertNotSame($point, $pointCopy);
        static::assertNotSame($line, $lineCopy);
        static::assertNotSame($point, $lineCopy->getPoint(0));
        static::assertNotSame($point, $lineCopy->getPoint(1));
        static::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        static::assertSame([1, 2], $pointCopy->toArray());
        static::assertSame([[1, 2], [1, 2]], $lineCopy->toArray());
        static::assertSame([1, 2], $point->toArray());
        static::assertSame([[1, 2], [1, 2]], $line->toArray());
        static::assertSame($pointReference, $pointCopy->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XY, $pointCopy->getDimension());
        static::assertSame(CoordinateDimensionEnum::XY, $lineCopy->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $pointCopy->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $lineCopy->getFamily());
        static::assertNotSame($emptyLine, $emptyCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame($lineReference, $emptyCopy->getSpatialReference());
        static::assertSame([], $emptyLine->toArray());
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

        static::assertNotSame($point, $pointCopy);
        static::assertNotSame($line, $lineCopy);
        static::assertNotSame($point, $lineCopy->getPoint(0));
        static::assertNotSame($point, $lineCopy->getPoint(1));
        static::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        static::assertSame([1, 2], $pointCopy->toArray());
        static::assertSame([[1, 2], [1, 2]], $lineCopy->toArray());
        static::assertSame([1, 2], $point->toArray());
        static::assertSame([[1, 2], [1, 2]], $line->toArray());
        static::assertSame($pointReference, $pointCopy->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XY, $pointCopy->getDimension());
        static::assertSame(CoordinateDimensionEnum::XY, $lineCopy->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $pointCopy->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $lineCopy->getFamily());
        static::assertNotSame($emptyLine, $emptyCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame($lineReference, $emptyCopy->getSpatialReference());
        static::assertSame([], $emptyLine->toArray());
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

        static::assertNotSame($point, $pointCopy);
        static::assertNotSame($line, $lineCopy);
        static::assertNotSame($point, $lineCopy->getPoint(0));
        static::assertNotSame($point, $lineCopy->getPoint(1));
        static::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        static::assertSame([1, 2, 7], $pointCopy->toArray());
        static::assertSame([[1, 2, 7], [1, 2, 7]], $lineCopy->toArray());
        static::assertSame([1, 2, 7], $point->toArray());
        static::assertSame([[1, 2, 7], [1, 2, 7]], $line->toArray());
        static::assertSame($pointReference, $pointCopy->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XYM, $pointCopy->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $lineCopy->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $pointCopy->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $lineCopy->getFamily());
        static::assertNotSame($emptyLine, $emptyCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame($lineReference, $emptyCopy->getSpatialReference());
        static::assertSame([], $emptyLine->toArray());
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

        static::assertNotSame($point, $pointCopy);
        static::assertNotSame($line, $lineCopy);
        static::assertNotSame($point, $lineCopy->getPoint(0));
        static::assertNotSame($point, $lineCopy->getPoint(1));
        static::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        static::assertSame([1, 2, 7], $pointCopy->toArray());
        static::assertSame([[1, 2, 7], [1, 2, 7]], $lineCopy->toArray());
        static::assertSame([1, 2, 7], $point->toArray());
        static::assertSame([[1, 2, 7], [1, 2, 7]], $line->toArray());
        static::assertSame($pointReference, $pointCopy->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XYM, $pointCopy->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $lineCopy->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $pointCopy->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $lineCopy->getFamily());
        static::assertNotSame($emptyLine, $emptyCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame($lineReference, $emptyCopy->getSpatialReference());
        static::assertSame([], $emptyLine->toArray());
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

        static::assertNotSame($point, $pointCopy);
        static::assertNotSame($line, $lineCopy);
        static::assertNotSame($point, $lineCopy->getPoint(0));
        static::assertNotSame($point, $lineCopy->getPoint(1));
        static::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        static::assertSame([1, 2, 3], $pointCopy->toArray());
        static::assertSame([[1, 2, 3], [1, 2, 3]], $lineCopy->toArray());
        static::assertSame([1, 2, 3], $point->toArray());
        static::assertSame([[1, 2, 3], [1, 2, 3]], $line->toArray());
        static::assertSame($pointReference, $pointCopy->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XYZ, $pointCopy->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZ, $lineCopy->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $pointCopy->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $lineCopy->getFamily());
        static::assertNotSame($emptyLine, $emptyCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame($lineReference, $emptyCopy->getSpatialReference());
        static::assertSame([], $emptyLine->toArray());
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

        static::assertNotSame($point, $pointCopy);
        static::assertNotSame($line, $lineCopy);
        static::assertNotSame($point, $lineCopy->getPoint(0));
        static::assertNotSame($point, $lineCopy->getPoint(1));
        static::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        static::assertSame([1, 2, 3], $pointCopy->toArray());
        static::assertSame([[1, 2, 3], [1, 2, 3]], $lineCopy->toArray());
        static::assertSame([1, 2, 3], $point->toArray());
        static::assertSame([[1, 2, 3], [1, 2, 3]], $line->toArray());
        static::assertSame($pointReference, $pointCopy->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XYZ, $pointCopy->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZ, $lineCopy->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $pointCopy->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $lineCopy->getFamily());
        static::assertNotSame($emptyLine, $emptyCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame($lineReference, $emptyCopy->getSpatialReference());
        static::assertSame([], $emptyLine->toArray());
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

        static::assertNotSame($point, $pointCopy);
        static::assertNotSame($line, $lineCopy);
        static::assertNotSame($point, $lineCopy->getPoint(0));
        static::assertNotSame($point, $lineCopy->getPoint(1));
        static::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        static::assertSame([1, 2, 3, 7], $pointCopy->toArray());
        static::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $lineCopy->toArray());
        static::assertSame([1, 2, 3, 7], $point->toArray());
        static::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $line->toArray());
        static::assertSame($pointReference, $pointCopy->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XYZM, $pointCopy->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $lineCopy->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $pointCopy->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $lineCopy->getFamily());
        static::assertNotSame($emptyLine, $emptyCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame($lineReference, $emptyCopy->getSpatialReference());
        static::assertSame([], $emptyLine->toArray());
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

        static::assertNotSame($point, $pointCopy);
        static::assertNotSame($line, $lineCopy);
        static::assertNotSame($point, $lineCopy->getPoint(0));
        static::assertNotSame($point, $lineCopy->getPoint(1));
        static::assertNotSame($lineCopy->getPoint(0), $lineCopy->getPoint(1));
        static::assertSame([1, 2, 3, 7], $pointCopy->toArray());
        static::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $lineCopy->toArray());
        static::assertSame([1, 2, 3, 7], $point->toArray());
        static::assertSame([[1, 2, 3, 7], [1, 2, 3, 7]], $line->toArray());
        static::assertSame($pointReference, $pointCopy->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(0)->getSpatialReference());
        static::assertSame($pointReference, $lineCopy->getPoint(1)->getSpatialReference());
        static::assertSame($lineReference, $lineCopy->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XYZM, $pointCopy->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $lineCopy->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $pointCopy->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $lineCopy->getFamily());
        static::assertNotSame($emptyLine, $emptyCopy);
        static::assertSame([], $emptyCopy->toArray());
        static::assertSame($lineReference, $emptyCopy->getSpatialReference());
        static::assertSame([], $emptyLine->toArray());
    }
}
