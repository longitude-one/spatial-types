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

namespace LongitudeOne\SpatialTypes\Types\Collection;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Types\AbstractSpatialType;

/**
 * Common line-string collection for multi-line strings.
 *
 * @internal
 */
abstract class AbstractLineStringCollection extends AbstractSpatialType
{
    /** @var LineStringInterface[] */
    protected array $lineStrings = [];

    /**
     * @param array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface>|LineStringInterface $lineString Line string or its point tuples
     *
     * @throws SpatialTypeExceptionInterface When the member is incompatible
     */
    protected function addLineStringMember(array|LineStringInterface $lineString): static
    {
        if (is_array($lineString)) {
            $lineString = $this->createLineStringFromCoordinates($lineString);
        }

        if (!$this->hasSameDimension($lineString)) {
            throw new InvalidDimensionException('The line string dimension is not compatible with the dimension of the current line-string collection.');
        }

        $this->assertSameSpatialReference($lineString, 'line string');

        $this->assertSameFamily($lineString, 'The line string family is not compatible with the family of the current line-string collection.');

        $this->lineStrings[] = $lineString;

        return $this;
    }

    /**
     * @param array<array<array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|PointInterface>>|LineStringInterface[] $lineStrings Line strings or their point tuples
     */
    protected function addLineStringMembers(array $lineStrings): static
    {
        foreach ($lineStrings as $lineString) {
            $this->addLineStringMember($lineString);
        }

        return $this;
    }

    /** @return LineStringInterface[] */
    protected function lineStringMembers(): array
    {
        return $this->lineStrings;
    }
}
