# Spatial Types

[![Release](https://img.shields.io/github/v/release/longitude-one/spatial-types?include_prereleases&sort=semver&label=release)](https://github.com/longitude-one/spatial-types/releases)
[![PHP](https://img.shields.io/packagist/php-v/longitude-one/spatial-types?label=php)](https://packagist.org/packages/longitude-one/spatial-types)
[![License](https://img.shields.io/packagist/l/longitude-one/spatial-types)](https://github.com/longitude-one/spatial-types/blob/main/LICENSE)
[![CI](https://github.com/longitude-one/spatial-types/actions/workflows/ci.yml/badge.svg)](https://github.com/longitude-one/spatial-types/actions/workflows/ci.yml)
[![Coverage](https://codecov.io/gh/longitude-one/spatial-types/branch/main/graph/badge.svg)](https://codecov.io/gh/longitude-one/spatial-types/tree/main)
[![Monthly downloads](https://img.shields.io/packagist/dm/longitude-one/spatial-types)](https://packagist.org/packages/longitude-one/spatial-types)
[![Documentation](https://img.shields.io/badge/docs-v1-blue)](https://www.longitude-one.fr/spatial-types/v1/index.html)

Create and manipulate spatial objects such as points, lines and polygons in PHP.
The library provides mutable and immutable types for geometric and geographic data;
use spatial-validator when you need explicit geometric validation.

## Installation

```bash
composer require longitude-one/spatial-types:^1.0.0-RC.1
```

## Usage

### Immutable point

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;

$point = new PointImmutable(1, 2);
```

### Mutable point

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;

$point = new Point(1, 2);
```

## Documentation

Read the [official documentation](https://www.longitude-one.fr/spatial-types/v1/index.html).

## Geometry and Geography

Geometry and Geography are distinct spatial families. Immutable types use separate
`Geometry` and `Geography` namespaces. Mutable types share the dimension namespace;
select Geography with the `family` constructor argument.

XY, XYZ, XYM and XYZM types live in `Dimension2`, `Dimension3z`, `Dimension3m`
and `Dimension4zm`, respectively. Declaring a spatial reference does not transform coordinates.

## Contributing

See [Contributing](CONTRIBUTING.md) for development setup, checks and contribution guidelines.
