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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\GeoParser\Exception\RangeException as GeoParserRangeException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable as XyPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable as XymPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable as XyzPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable as XyzmPoint;
use PHPUnit\Framework\TestCase;

/**
 * Removing unused spatial exceptions preserves active coordinate error translation.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\PointBehaviorTrait
 */
class Issue86ParsingExceptionTest extends TestCase
{
    /** Longitude parsing still exposes InvalidValueException in XY. */
    public function testXyLongitudeErrorRetainsParserCause(): void
    {
        try {
            new XyPoint('181W', 0);
            static::fail('An invalid longitude must fail parsing.');
        } catch (InvalidValueException $exception) {
            static::assertSame('Out of range longitude value, longitude must be between -180 and 180, got "181W".', $exception->getMessage());
            static::assertSame(180, $exception->getCode());
            static::assertInstanceOf(GeoParserRangeException::class, $exception->getPrevious());
        }
    }

    /** Minute parsing still exposes InvalidValueException in XYM. */
    public function testXymMinuteErrorRetainsParserCause(): void
    {
        try {
            new XymPoint('79:60:55.832W', 0, 8);
            static::fail('Invalid minutes must fail parsing.');
        } catch (InvalidValueException $exception) {
            static::assertSame('Out of range minute value, minute must be between 0 and 59, got "79:60:55.832W".', $exception->getMessage());
            static::assertSame(3600, $exception->getCode());
            static::assertInstanceOf(GeoParserRangeException::class, $exception->getPrevious());
        }
    }

    /** Latitude parsing still exposes InvalidValueException in XYZ. */
    public function testXyzLatitudeErrorRetainsParserCause(): void
    {
        try {
            new XyzPoint(0, '100N', 12);
            static::fail('An invalid latitude must fail parsing.');
        } catch (InvalidValueException $exception) {
            static::assertSame('Out of range latitude value, latitude must be between -90 and 90, got "100N".', $exception->getMessage());
            static::assertSame(90, $exception->getCode());
            static::assertInstanceOf(GeoParserRangeException::class, $exception->getPrevious());
        }
    }

    /** Second parsing still exposes InvalidValueException in XYZM. */
    public function testXyzmSecondErrorRetainsParserCause(): void
    {
        try {
            new XyzmPoint('79:55:60.832W', 0, 12, 8);
            static::fail('Invalid seconds must fail parsing.');
        } catch (InvalidValueException $exception) {
            static::assertSame('Out of range second value, second must be between 0 and 59, got "79:55:60.832W".', $exception->getMessage());
            static::assertSame(60, $exception->getCode());
            static::assertInstanceOf(GeoParserRangeException::class, $exception->getPrevious());
        }
    }
}
