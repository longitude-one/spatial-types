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

namespace LongitudeOne\SpatialTypes\Internal\Types\Collection;

use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutablePointInterface;
use LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialTypeImmutable;
use LongitudeOne\SpatialTypes\Types\ImmutableComponentGuard;

/**
 * Common line-string collection for multi-line strings.
 *
 * @internal
 */
abstract class AbstractLineStringCollectionImmutable extends AbstractSpatialTypeImmutable
{
    /** @var ImmutableLineStringInterface[] */
    protected array $lineStrings = [];

    /**
     * @param array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface>|ImmutableLineStringInterface $lineString Line string or its point tuples
     *
     * @throws SpatialTypeExceptionInterface When the member is incompatible
     */
    protected function addLineStringMember(array|ImmutableLineStringInterface $lineString): static
    {
        if (is_array($lineString)) {
            $lineString = $this->createLineStringFromCoordinates($lineString);
        }

        ImmutableComponentGuard::check($lineString);
        $this->lineStrings[] = $lineString;

        return $this;
    }

    /**
     * @param array<array<array{0: null|float|int|string, 1: null|float|int|string, 2 ?: null|float|int, 3 ?: null|float|int}|ImmutablePointInterface>>|ImmutableLineStringInterface[] $lineStrings Line strings or their point tuples
     */
    protected function addLineStringMembers(array $lineStrings): static
    {
        foreach ($lineStrings as $lineString) {
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
