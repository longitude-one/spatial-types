<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024
 * Copyright Longitude One 2024
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Types;

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\LineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MultiLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\PointInterface;
use LongitudeOne\SpatialTypes\Trait\LineStringTrait;

abstract class AbstractMultiLineString extends AbstractSpatialType implements MultiLineStringInterface
{
    use LineStringTrait;

    /**
     * AbstractMultiLineString constructor.
     *
     * @param (array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[]|LineStringInterface|PointInterface[])[] $lineStrings lineStrings of the multiLineString
     * @param null|int                                                                                                                                                $srid        Spatial Reference Identifier
     *
     * @throws InvalidDimensionException when the point dimension is not compatible with the multiLineStlineString dimension
     * @throws InvalidSridException      when the point SRID is not compatible with the multiLineStlineString SRID
     * @throws InvalidValueException     when coordinates of the point are invalid
     * @throws MissingValueException     when the point is missing
     */
    public function __construct(array $lineStrings, ?int $srid = null)
    {
        $this->setSrid($srid);
        $this->addLineStrings($lineStrings);
    }

    /**
     * Add a line string to the spatial collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[]|LineStringInterface|PointInterface[] $lineString line string to add
     *
     * @throws InvalidValueException when the line string dimension is not compatible with the current dimension
     */
    public function addLineString(array|LineStringInterface $lineString): static
    {
        try {
            return $this->traitAddLineString($lineString);
        } catch (InvalidFamilyException $e) {
            throw new InvalidFamilyException('The line string family is not compatible with the family of the current multilinestring.', $e->getCode(), $e);
        }
    }

    /**
     * Add line strings to the spatial collection.
     *
     * @param array{0: float|int|string, 1: float|int|string, 2 ?: null|float|int, 3 ?: null|\DateTimeInterface|float|int}[][]|LineStringInterface[]|PointInterface[][] $lineStrings the ring to add
     *
     * @throws SpatialTypeExceptionInterface when something is wrong during the addition
     */
    public function addLineStrings(array $lineStrings): static
    {
        return $this->traitAddLineStrings($lineStrings);
    }

    /**
     * Get the elements of this multiLineString.
     *
     * @return LineStringInterface[]
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
    public function getLineString(int $index): LineStringInterface
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
     * @return LineStringInterface[]
     */
    public function getLineStrings(): array
    {
        return $this->traitGetLineStrings();
    }

    /**
     * Is the multiLineString empty?
     */
    public function isEmpty(): bool
    {
        return empty($this->getLineStrings());
    }

    /**
     * Return an array representation of the multiLineString.
     *
     * @return (\DateTimeInterface|float|int)[][][]
     */
    public function toArray(): array
    {
        $lineStrings = $this->getLineStrings();

        return array_map(
            static fn (LineStringInterface $lineString) => $lineString->toArray(),
            $lineStrings
        );
    }
}
