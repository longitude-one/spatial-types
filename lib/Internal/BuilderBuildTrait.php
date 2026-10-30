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
