# Spatial Types Library

![longitude-one/spatial--types](https://img.shields.io/badge/longitude--one-spatial--types-blue)
![Stable release](https://img.shields.io/github/v/release/longitude-one/spatial-types)
![Minimum PHP Version](https://img.shields.io/packagist/php-v/longitude-one/spatial-types.svg?maxAge=3600)
[![Packagist License](https://img.shields.io/packagist/l/longitude-one/spatial-types)](https://github.com/longitude-one/spatial-types/blob/main/LICENSE)

[![PHP test](https://github.com/longitude-one/spatial-types/actions/workflows/ci.yml/badge.svg)](https://github.com/longitude-one/spatial-types/actions/workflows/ci.yml)
[![Downloads](https://img.shields.io/packagist/dm/longitude-one/spatial-types.svg)](https://packagist.org/packages/longitude-one/spatial-types)
[![Coverage Status](https://coveralls.io/repos/github/longitude-one/spatial-types/badge.svg?branch=main)](https://coveralls.io/github/longitude-one/spatial-types?branch=main)

Spatial-types is a PHP library for creating and manipulating spatial objects such as points, lines, and polygons. It provides mutable and immutable types to represent geometric and
geographic data.

## Installation

```bash
composer require longitude-one/spatial-types:^1.0.0-RC.1
```

## Documentation

Read the [official documentation](https://www.longitude-one.fr/spatial-types/v1/index.html).

## Examples

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
