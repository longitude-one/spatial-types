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

namespace LongitudeOne\SpatialTypes\Internal\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableMultiLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\Collection\AbstractLineStringCollectionImmutable;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Value\Coordinates;

/**
 * Abstract MultiLineString class.
 *
 * @internal this class provides common behaviour for geometry and geography multi-line strings
 */
abstract class AbstractMultiLineStringImmutable extends AbstractLineStringCollectionImmutable implements ImmutableMultiLineStringInterface
{
    /**
     * AbstractMultiLineStringImmutable constructor.
     *
     * @param (array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[]|ImmutableLineStringInterface|ImmutablePointInterface[])[] $lineStrings lineStrings of the multiLineString
     * @param int|SpatialReference                                                                                                                                             $srid        Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the multiLineStlineString dimension
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $lineStrings, int|SpatialReference $srid = 0)
    {
        $this->initializeSpatialReference($srid);
        $this->addLineStrings($lineStrings);
    }

    /**
     * Get the elements of this multiLineString.
     *
     * @return ImmutableLineStringInterface[]
     */
    public function getElements(): array
    {
        return $this->getLineStrings();
    }

    /**
     * Get the lineString of the multiLineString.
     *
     * @param int $index Index of the lineString
     */
    public function getLineString(int $index): ImmutableLineStringInterface
    {
        if (empty($this->getLineStrings())) {
            throw new OutOfBoundsException('The current collection of lineStrings is empty.');
        }

        $index = $index % count($this->getLineStrings());

        if ($index < 0) {
            $index = count($this->getLineStrings()) + $index;
        }

        return $this->getLineStrings()[$index];
    }

    /**
     * Get the line strings of the spatial collection.
     *
     * @return ImmutableLineStringInterface[]
     */
    public function getLineStrings(): array
    {
        return $this->lineStringMembers();
    }

    /**
     * Return an array representation of the multiLineString.
     *
     * @return (null|float|int)[][][]
     */
    public function toArray(): array
    {
        $lineStrings = $this->getLineStrings();

        return array_map(
            static fn (ImmutableLineStringInterface $lineString) => $lineString->toArray(),
            $lineStrings
        );
    }

    /**
     * Return a deep copy of this multi-line string with one replacement line string.
     *
     * The original multi-line string and all its line strings remain unchanged.
     * The replacement coordinates are created through the existing family,
     * dimension, and Spatial Reference Identifier (SRID) context.
     *
     * @param int                                                                                                        $lineStringIndex index of the line string to replace; negative indexes count from the end
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}> $coordinates     replacement line-string coordinates
     *
     * @throws OutOfBoundsException when the multi-line string has no line strings
     */
    public function withLineString(int $lineStringIndex, array $coordinates): static
    {
        $lineStringIndex = $this->normalizeLineStringIndex($lineStringIndex);
        $multiLineString = clone $this;
        $multiLineString->lineStrings = [];

        foreach ($this->lineStrings as $index => $lineString) {
            $multiLineString->addLineString($index === $lineStringIndex ? $coordinates : $lineString->withSpatialReference($lineString->getSpatialReference()));
        }

        return $multiLineString;
    }

    /**
     * Return a deep copy of this multi-line string with one replacement point.
     *
     * @param int         $lineStringIndex index of the line string to replace; negative indexes count from the end
     * @param int         $pointIndex      index of the point to replace; negative indexes count from the end
     * @param Coordinates $coordinates     replacement point coordinates
     *
     * @throws OutOfBoundsException when the multi-line string has no line strings
     */
    public function withPoint(int $lineStringIndex, int $pointIndex, Coordinates $coordinates): static
    {
        $lineStringIndex = $this->normalizeLineStringIndex($lineStringIndex);
        $multiLineString = clone $this;
        $multiLineString->lineStrings = array_map(
            static fn (ImmutableLineStringInterface $lineString): ImmutableLineStringInterface => $lineString->withSrid($lineString->getSrid()),
            $this->lineStrings
        );
        $multiLineString->lineStrings[$lineStringIndex] = $multiLineString->lineStrings[$lineStringIndex]->withPoint($pointIndex, $coordinates);

        return $multiLineString;
    }

    /**
     * Return a deep copy declared in the supplied spatial reference.
     *
     * @param SpatialReference $reference Target spatial reference
     */
    public function withSpatialReference(SpatialReference $reference): static
    {
        $multiLineString = parent::withSpatialReference($reference);
        $multiLineString->lineStrings = array_map(
            static fn (ImmutableLineStringInterface $lineString): ImmutableLineStringInterface => $lineString->withSpatialReference($reference),
            $this->lineStrings
        );

        return $multiLineString;
    }

    /**
     * Return a copy of this multi-line string with the given Spatial Reference Identifier (SRID).
     *
     * Every line string, and therefore every contained point, is copied with the
     * requested SRID to preserve the aggregate's internal SRID consistency.
     *
     * @param int $srid Spatial Reference Identifier
     */
    public function withSrid(int $srid): static
    {
        return $this->withSpatialReference(SpatialReference::fromSrid($srid));
    }

    /**
     * Add a line string to the spatial collection.
     *
     * @param array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[]|ImmutableLineStringInterface|ImmutablePointInterface[] $lineString line string to add
     *
     * @throws InvalidValueException when the line string dimension is not compatible with the current dimension
     */
    protected function addLineString(array|ImmutableLineStringInterface $lineString): static
    {
        return $this->addLineStringMember($lineString);
    }

    /**
     * Add line strings to the spatial collection.
     *
     * @param array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}[][]|ImmutableLineStringInterface[]|ImmutablePointInterface[][] $lineStrings the ring to add
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition
     */
    protected function addLineStrings(array $lineStrings): static
    {
        return $this->addLineStringMembers($lineStrings);
    }

    /**
     * Normalize a line-string index according to the collection accessor convention.
     *
     * @param int $lineStringIndex line-string index to normalize
     *
     * @throws OutOfBoundsException when the multi-line string has no line strings
     */
    private function normalizeLineStringIndex(int $lineStringIndex): int
    {
        $lineStringCount = count($this->lineStrings);
        if (0 === $lineStringCount) {
            throw new OutOfBoundsException('The current collection of lineStrings is empty.');
        }

        $lineStringIndex %= $lineStringCount;

        return $lineStringIndex < 0 ? $lineStringCount + $lineStringIndex : $lineStringIndex;
    }
}
