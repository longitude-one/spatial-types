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

namespace LongitudeOne\SpatialTypes\Factory\Hydrator;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Factory\Coordinates;
use LongitudeOne\SpatialTypes\Factory\SpatialContext;

/**
 * Hydrates point coordinates from an indexed array.
 */
final class CoordinatesHydrator
{
    /**
     * Hydrate coordinates from an indexed array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $coordinates coordinates
     *
     * @throws InvalidDimensionException when the array contains too many coordinates
     * @throws InvalidValueException     when a Z or M coordinate is not numeric
     * @throws MissingValueException     when a required coordinate is missing
     */
    public function hydrate(array $coordinates, SpatialContext $context): Coordinates
    {
        $coordinateCount = $context->dimension->coordinateCount();
        if (count($coordinates) > $coordinateCount) {
            throw new InvalidDimensionException(sprintf('The array must contain exactly %d coordinates to create a %s point.', $coordinateCount, $context->dimension->value));
        }

        foreach (range(0, $coordinateCount - 1) as $index) {
            if (!array_key_exists($index, $coordinates) || null === $coordinates[$index]) {
                throw new MissingValueException(sprintf('The %s coordinate of array is missing.', match ($index) {
                    0 => 'first',
                    1 => 'second',
                    2 => 'third',
                    3 => 'fourth',
                    default => 'unknown',
                }));
            }
        }

        $zIndex = $context->dimension->zIndex();
        $mIndex = $context->dimension->mIndex();

        return new Coordinates(
            $coordinates[0],
            $coordinates[1],
            null === $zIndex ? null : $this->numericCoordinate($coordinates[$zIndex], 'Z'),
            null === $mIndex ? null : $this->numericCoordinate($coordinates[$mIndex], 'M')
        );
    }

    /**
     * Ensure a Z or M coordinate is numeric.
     *
     * @param mixed  $coordinate coordinate to validate
     * @param string $name       coordinate name used in the error message
     *
     * @return float|int validated coordinate
     *
     * @throws InvalidValueException when the coordinate is not numeric
     */
    private function numericCoordinate(mixed $coordinate, string $name): float|int
    {
        if (!is_float($coordinate) && !is_int($coordinate)) {
            throw new InvalidValueException(sprintf('The %s coordinate must be a number.', $name));
        }

        return $coordinate;
    }
}
