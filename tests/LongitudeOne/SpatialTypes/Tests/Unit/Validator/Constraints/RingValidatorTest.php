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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Validator\Constraints;

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Validator\Constraints\FirstPointEqualsLastPoint;
use LongitudeOne\SpatialTypes\Validator\Constraints\MinimumPointCount;
use LongitudeOne\SpatialTypes\Validator\Constraints\Ring;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

/**
 * @covers \LongitudeOne\SpatialTypes\Validator\Constraints\RingValidator
 *
 * @internal
 */
class RingValidatorTest extends TestCase
{
    /** Test that four closed points form a valid ring. */
    public function testRingAcceptsFourClosedPoints(): void
    {
        $violations = Validation::createValidator()->validate(
            new LineString([[0, 0], [1, 0], [0, 1], [0, 0]]),
            new Ring()
        );

        static::assertCount(0, $violations);
    }

    /** Test that a ring must have equal first and last points. */
    public function testRingReportsTheClosureConstraint(): void
    {
        $violations = Validation::createValidator()->validate(
            new LineString([[0, 0], [1, 0], [0, 1], [2, 2]]),
            new Ring()
        );

        static::assertCount(1, $violations);
        static::assertSame('A linear ring must have equal first and last points.', $violations->get(0)->getMessage());
    }

    /** Test that a ring needs at least four points. */
    public function testRingReportsTheMinimumPointConstraint(): void
    {
        $violations = Validation::createValidator()->validate(
            new LineString([[0, 0], [1, 0], [0, 0]]),
            new Ring()
        );

        static::assertCount(1, $violations);
        static::assertSame('A linear ring must contain at least 4 points.', $violations->get(0)->getMessage());
    }

    /** Test that each structural constraint is usable independently. */
    public function testStructuralConstraintsCanBeUsedIndependently(): void
    {
        $lineString = new LineString([[0, 0], [1, 0], [0, 1], [2, 2]]);
        $validator = Validation::createValidator();

        static::assertCount(0, $validator->validate($lineString, new MinimumPointCount()));
        static::assertCount(1, $validator->validate($lineString, new FirstPointEqualsLastPoint()));
    }
}
