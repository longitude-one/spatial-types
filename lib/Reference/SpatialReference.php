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

namespace LongitudeOne\SpatialTypes\Reference;

/**
 * Identifies the spatial reference system associated with a spatial value.
 *
 * An SRID alone is an implementation identifier. The optional authority makes
 * that identifier unambiguous when it is known (for example EPSG:4326).
 */
final readonly class SpatialReference
{
    /**
     * @param int         $identifier Spatial reference identifier
     * @param string|null $authority  Naming authority, for example "EPSG"
     */
    public function __construct(
        public int $identifier,
        public ?string $authority = null,
    ) {
    }

    /**
     * Create an EPSG reference.
     *
     * @param int $code EPSG code
     */
    public static function epsg(int $code): self
    {
        return new self($code, 'EPSG');
    }

    /**
     * Create an authority-independent reference from a legacy SRID.
     *
     * @param int $srid Legacy SRID
     */
    public static function fromSrid(int $srid): self
    {
        return new self($srid);
    }

    /**
     * Compare two spatial references.
     *
     * @param self $other Reference to compare
     */
    public function equals(self $other): bool
    {
        return $this->identifier === $other->identifier
            && $this->authority === $other->authority;
    }

    /**
     * Return the traditional SRID representation.
     */
    public function srid(): int
    {
        return $this->identifier;
    }
}
