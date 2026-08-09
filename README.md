# Spatial Types Library

Implement spatial PHP types and their geometric and geographic classes.

If you want to persist spatial data in a database,
you should use the [longitude-one/doctrine2-spatial](https://github.com/longitude-one/doctrine2-spatial) package.

## Current status

![longitude-one/spatial--types](https://img.shields.io/badge/longitude--one-spatial--types-blue)
![Stable release](https://img.shields.io/github/v/release/longitude-one/spatial-types)
![Minimum PHP Version](https://img.shields.io/packagist/php-v/longitude-one/spatial-types.svg?maxAge=3600)
[![Packagist License](https://img.shields.io/packagist/l/longitude-one/spatial-types)](https://github.com/longitude-one/spatial-types/blob/main/LICENSE)

[![Last integration test](https://github.com/longitude-one/spatial-types/actions/workflows/php-oldest.yaml/badge.svg)](https://github.com/longitude-one/spatial-types/actions/workflows/php-oldest.yaml)
[![Last integration test](https://github.com/longitude-one/spatial-types/actions/workflows/php-oldest.yaml/badge.svg)](https://github.com/longitude-one/spatial-types/actions/workflows/php-latests.yaml)
[![Maintainability](https://api.codeclimate.com/v1/badges/494c578572cae00ec1db/maintainability)](https://codeclimate.com/github/longitude-one/spatial-types/maintainability)
[![Downloads](https://img.shields.io/packagist/dm/longitude-one/spatial-types.svg)](https://packagist.org/packages/longitude-one/spatial-types)
[![Coverage Status](https://coveralls.io/repos/github/longitude-one/spatial-types/badge.svg?branch=main)](https://coveralls.io/github/longitude-one/spatial-types?branch=main)

## Installation

```bash
composer require longitude-one/spatial-types
```

## Usage

```php
use LongitudeOne\Spatial\Types\Geometry\Point;

$point = new Point(1, 2);
echo $point->getX(); // 1
echo $point->getY(); // 2

$lineString = new LineString([
    new Point(1, 2),
    new Point(3, 4),
    new Point(5, 6),
], 4326);
$lineString->getSrid(); // 4326
```

For the complete catalogue of concrete types, constructors, accessors, and
mutability rules, see [Instantiable spatial types](docs/instantiable-spatial-types.md).

## SRID

Every spatial object always has an integer SRID. `SpatialInterface::DEFAULT_SRID`
is `0`, the default assigned by this library whenever no SRID is supplied. This
follows SQL/MM, which specifies SRID 0 for constructors without an SRID and
leaves its semantics to the implementation.

Here, SRID 0 means that the reference system is unspecified. It can therefore
be combined with a non-zero SRID in a collection; two non-zero, distinct SRIDs
remain incompatible. This preserves a concrete, serializable SRID while keeping
the default useful for spatial values whose reference system is not known yet.
