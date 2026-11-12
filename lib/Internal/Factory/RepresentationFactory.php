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

namespace LongitudeOne\SpatialTypes\Internal\Factory;

use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;

/** Dispatches constructor arguments without converting supplied components.
 * @internal
 */
final class RepresentationFactory
{
    /**
     * @template TImmutable of ImmutableSpatialInterface
     * @template TMutable of MutableSpatialInterface
     *
     * @param string  $immutableClass     Immutable constructor
     * @param string  $mutableClass       Mutable constructor
     * @param mixed[] $immutableArguments Immutable constructor arguments
     * @param mixed[] $mutableArguments   Mutable constructor arguments, including family
     *
     * @phpstan-param class-string<TImmutable> $immutableClass
     * @phpstan-param class-string<TMutable> $mutableClass
     * @phpstan-param list<mixed> $immutableArguments
     * @phpstan-param list<mixed> $mutableArguments
     *
     * @return TImmutable|TMutable
     */
    public static function create(string $immutableClass, string $mutableClass, array $immutableArguments, array $mutableArguments): ImmutableSpatialInterface|MutableSpatialInterface
    {
        if (SpatialFactory::isMutable()) {
            return new $mutableClass(...$mutableArguments);
        }

        return new $immutableClass(...$immutableArguments);
    }
}
