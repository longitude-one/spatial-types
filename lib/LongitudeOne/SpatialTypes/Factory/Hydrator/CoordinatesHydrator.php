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
 *
 * @internal this hydrator supports the internal factory pipeline
 */
final class CoordinatesHydrator
{
    /**
     * Hydrate coordinates from an indexed array.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|float|int} $coordinates coordinates
     * @param SpatialContext                                                                            $context     expected dimension and target SRID
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

        $this->validateRequiredCoordinates($coordinates, $coordinateCount);

        return new Coordinates(
            $coordinates[0],
            $coordinates[1],
            $this->optionalNumericCoordinate($coordinates, $context->dimension->zIndex(), 'Z'),
            $this->optionalNumericCoordinate($coordinates, $context->dimension->mIndex(), 'M')
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

    /**
     * Return an optional Z or M coordinate for the current dimension.
     *
     * A null index means that the dimension does not contain this coordinate.
     * Otherwise, validate the already-required indexed value as numeric before
     * returning it to the factory DTO.
     *
     * @param array<array-key, mixed> $coordinates coordinates to read
     * @param null|int                $index       coordinate index, if supported by the dimension
     * @param string                  $name        coordinate name used in the error message
     *
     * @return null|float|int the coordinate, or null when absent from the dimension
     *
     * @throws InvalidValueException when the coordinate is not numeric
     */
    private function optionalNumericCoordinate(array $coordinates, ?int $index, string $name): float|int|null
    {
        if (null === $index) {
            return null;
        }

        return $this->numericCoordinate($coordinates[$index], $name);
    }

    /**
     * Ensure that every coordinate required by the dimension is present.
     *
     * @param array<array-key, mixed> $coordinates     coordinates to validate
     * @param int                     $coordinateCount number of required coordinates
     *
     * @throws MissingValueException when a required coordinate is missing
     */
    private function validateRequiredCoordinates(array $coordinates, int $coordinateCount): void
    {
        $coordinateNames = ['first', 'second', 'third', 'fourth'];

        foreach (range(0, $coordinateCount - 1) as $index) {
            if (!array_key_exists($index, $coordinates) || null === $coordinates[$index]) {
                throw new MissingValueException(sprintf('The %s coordinate of array is missing.', $coordinateNames[$index] ?? 'unknown'));
            }
        }
    }
}
