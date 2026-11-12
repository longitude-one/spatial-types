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

namespace LongitudeOne\SpatialTypes\Internal;

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;

/** @internal Methods of the one public SpatialBuilder class. */
trait BuilderBuildTrait
{
    /**
     * Build.
     */
    public function build(): SpatialInterface
    {
        if (null === $this->aggregate) {
            throw new InvalidValueException('Select an aggregate before building.');
        }

        return $this->createType($this->aggregate, [$this->components]);
    }
}
