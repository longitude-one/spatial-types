# Spatial validation

`spatial-types` represents spatial values without automatic functional validation.
Constructors and immutable replacement methods retain technical typing checks:
member interfaces, numeric coordinate tuples, required ordinates, and the
coordinate dimension of a concrete point. EMPTY points still require all
ordinates to be null together. Indexed operations still reject empty containers.

Geometry validity is an explicit consumer operation. Functional rules such as
ring closure, triangle cardinality, geographic numeric ranges, aggregate
family/dimension/reference compatibility, curve continuity and surface topology
are not enforced by this package.

Constraints and validators formerly under `LongitudeOne\SpatialTypes\Validator`
now belong to [spatial-validator](https://github.com/longitude-one/spatial-validator).
This package neither depends on that library nor invokes it. Symfony Validator
is no longer a dependency of `spatial-types`.

Coordinate strings still use `geo-parser`; malformed or out-of-range textual
expressions can fail during parsing. Numeric coordinates bypass that parser.

Removing the former validator namespace and automatic rejection behavior is a
breaking change. Applications requiring valid geometry must perform validation
explicitly before consuming the represented values.
