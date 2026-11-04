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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Types\Dimension2 as XY;
use LongitudeOne\SpatialTypes\Types\Dimension3z as XYZ;
use PHPUnit\Framework\TestCase;

/** Regression coverage for #78.
 * @internal
 *
 * @coversNothing
 */
class MutableMultiPointDimensionMutationTest extends TestCase
{
    /** Rejection preserves membership and subsequent context propagation. */
    public function testAddRejectsAtomically(): void
    {
        $member = new XY\Point(1, 2);
        $aggregate = new XY\MultiPoint([$member], 2154);
        $incompatible = new XYZ\Point(7, 8, 9);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->addElement($incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testInsertRejectsAtomically(): void
    {
        $member = new XY\Point(1, 2);
        $aggregate = new XY\MultiPoint([$member], 2154);
        $incompatible = new XYZ\Point(7, 8, 9);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->insertElement(1, $incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testSetallRejectsAtomically(): void
    {
        $member = new XY\Point(1, 2);
        $aggregate = new XY\MultiPoint([$member], 2154);
        $incompatible = new XYZ\Point(7, 8, 9);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->setElements([$member, $incompatible]);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }

    /** Rejection preserves membership and subsequent context propagation. */
    public function testSetRejectsAtomically(): void
    {
        $member = new XY\Point(1, 2);
        $aggregate = new XY\MultiPoint([$member], 2154);
        $incompatible = new XYZ\Point(7, 8, 9);
        $this->expectException(InvalidDimensionException::class);

        try {
            $aggregate->setElement(0, $incompatible);
        } finally {
            static::assertSame([$member], $aggregate->getElements());
            static::assertSame(2154, $aggregate->getSrid());
            $aggregate->setSrid(4326);
            static::assertSame(4326, $member->getSrid());
            static::assertSame(0, $incompatible->getSrid());
        }
    }
}
