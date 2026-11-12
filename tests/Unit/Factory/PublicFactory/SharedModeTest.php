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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory\PublicFactory;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geometry\PointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3m\Geography\LineStringFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\LineString;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

/** Shared selection and process-default contract.
 * @internal
 *
 * @coversNothing
 */
final class SharedModeTest extends TestCase
{
    /** Restore the shared mode for subsequent tests. */
    protected function tearDown(): void
    {
        SpatialFactory::immutable();
        parent::tearDown();
    }

    /** Direct constructors retain their representation while factory mode is mutable. */
    public function testDirectImmutableConstructionIgnoresSharedFactoryMode(): void
    {
        SpatialFactory::mutable();
        $point = new PointImmutable(0, null, 4326);
        $line = new LineStringImmutable([[0, null, 7]], 4326);

        static::assertSame([0, null], $point->toArray());
        static::assertSame([[0, null, 7]], $line->toArray());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable::class, $line->getPoint(0));
        static::assertTrue(SpatialFactory::isMutable());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    /** Fresh Process Defaults To Immutable Without Selecting AMode. */
    public function testFreshProcessUsesImmutableDefault(): void
    {
        static::assertFalse(SpatialFactory::isMutable());
        static::assertInstanceOf(PointImmutable::class, PointFactory::create());
        static::assertInstanceOf(LineStringImmutable::class, LineStringFactory::create([]));
    }

    /** Mode Commands Are Idempotent. */
    public function testModeCommandsAreIdempotent(): void
    {
        SpatialFactory::mutable();
        SpatialFactory::mutable();
        static::assertTrue(SpatialFactory::isMutable());
        SpatialFactory::immutable();
        SpatialFactory::immutable();
        static::assertFalse(SpatialFactory::isMutable());
    }

    /** Selection Is Shared Across Factories And Changes Only Later Creations. */
    public function testSelectionIsSharedAcrossFactoriesAndChangesOnlyLaterCreations(): void
    {
        SpatialFactory::immutable();
        static::assertFalse(SpatialFactory::isMutable());
        $first = PointFactory::create(0, null, 2154);
        SpatialFactory::mutable();
        static::assertTrue(SpatialFactory::isMutable());
        $second = PointFactory::create(1, 2, 4326);
        $line = LineStringFactory::create([[3, 4, 5]], 4326);
        SpatialFactory::immutable();
        static::assertFalse(SpatialFactory::isMutable());
        $third = PointFactory::create(6, 7, 3857);
        $immutableLine = LineStringFactory::create([[8, 9, 10]], 3857);

        static::assertInstanceOf(PointImmutable::class, $first);
        static::assertInstanceOf(Point::class, $second);
        static::assertInstanceOf(LineString::class, $line);
        static::assertInstanceOf(PointImmutable::class, $third);
        static::assertInstanceOf(LineStringImmutable::class, $immutableLine);
        $second->setX(20);
        static::assertSame([0, null], $first->toArray());
        static::assertSame(2154, $first->getSrid());
        static::assertSame([20, 2], $second->toArray());
        static::assertSame([[3, 4, 5]], $line->toArray());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame([6, 7], $third->toArray());
        static::assertSame([[8, 9, 10]], $immutableLine->toArray());
    }
}
