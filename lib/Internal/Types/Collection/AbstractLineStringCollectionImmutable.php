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

namespace LongitudeOne\SpatialTypes\Internal\Types\Collection;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialTypeImmutable;
use LongitudeOne\SpatialTypes\Internal\Types\ImmutableComponentGuard;

/**
 * Common line-string collection for multi-line strings.
 *
 * @internal
 */
abstract class AbstractLineStringCollectionImmutable extends AbstractSpatialTypeImmutable
{
    /** @var ImmutableLineStringInterface[] */
    protected array $lineStrings = [];

    /** Copy every descendant while retaining its individual spatial reference. */
    public function copy(): static
    {
        $copy = clone $this;
        $copy->lineStrings = array_map(
            static fn (ImmutableLineStringInterface $component): ImmutableLineStringInterface => self::copyComponent($component),
            $this->lineStrings
        );

        return $copy;
    }

    /**
     * @param array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}|ImmutablePointInterface>|ImmutableLineStringInterface $lineString Line string or its point tuples
     *
     * @throws SpatialTypeExceptionInterface When the member is incompatible
     */
    protected function addLineStringMember(array|ImmutableLineStringInterface $lineString): static
    {
        if (\is_array($lineString)) {
            $lineString = $this->createLineStringFromCoordinates($lineString);
        }

        ImmutableComponentGuard::check($lineString);
        $this->lineStrings[] = $lineString;

        return $this;
    }

    /**
     * @param array<array<array{0: float|int|string|null, 1: float|int|string|null, 2 ?: float|int|null, 3 ?: float|int|null}|ImmutablePointInterface>>|ImmutableLineStringInterface[] $lineStrings Line strings or their point tuples
     */
    protected function addLineStringMembers(array $lineStrings): static
    {
        foreach ($lineStrings as $lineString) {
            if (!\is_array($lineString) && !$lineString instanceof ImmutableLineStringInterface) {
                throw new InvalidValueException('MultiLineStringImmutable requires ImmutableLineStringInterface components or line-string coordinate arrays.');
            }
            $this->addLineStringMember($lineString);
        }

        return $this;
    }

    /** @return ImmutableLineStringInterface[] */
    protected function lineStringMembers(): array
    {
        return $this->lineStrings;
    }
}
