# Numeric point representation equality

`PointInterface::equalsTo()` compares representations, not spatial or topological
equality. Matching numeric ordinate values compare equal even when one is an
integer and the other a float. Comparisons neither modify nor normalize stored
coordinates.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;

$integer = new PointImmutable(1, 2, 4326);
$floating = new PointImmutable(1.0, 2.0, 4326);

$integer->equalsTo($floating); // true
$integer->toArray();          // [1, 2]
$floating->toArray();         // [1.0, 2.0]
```

The same rule applies to X, Y, Z and M in mutable and immutable points, in both
Geometry and Geography. There is no approximate tolerance. Null stays distinct
from zero. Different concrete classes, SRIDs, families or declared coordinate
layouts remain unequal, including extra axes whose values are null or zero.
EMPTY values retain those context distinctions; an EMPTY point never equals a
non-EMPTY point.

Existing curve closure and point-duplicate simplicity checks that call
`equalsTo()` observe this numeric equivalence. It does not add geometric
predicates, transform coordinates, remove axes or define new XY/3D semantics.

## Story #26 conformance evidence: approved R3 correction

This matrix records the approved representation contract from
[Story #64](https://github.com/longitude-one/spatial-types/issues/64), following
Alexandre's explicit 2026-10-30 decision. It supplements the broader
[Story #26 audit](https://github.com/longitude-one/spatial-types/issues/26);
it does not certify full normative conformance or resolve the remaining R3
geometric-predicate questions.

| Requirement | Implementation | Automated evidence | Status |
| --- | --- | --- | --- |
| Equivalent integer/float ordinates, with null distinct from zero | `lib/Types/PointBehaviorTrait.php::equalsTo()` and private ordinate comparison | `tests/Issue/NumericPointEqualityTest.php` | Implemented for XY/XYZ/XYM/XYZM, mutable/immutable and both families |
| Context, layout and concrete-class distinctions; EMPTY and partial positions; no mutation or tolerance | Existing context checks and EMPTY branch in the same trait | `tests/Issue/NumericPointEqualityTest.php` | Preserved |
| Numeric equivalence reaches existing closure and duplicate-position callers | LineString, CircularString, CompoundCurve and point-collection implementations | `tests/Issue/NumericEqualityPredicateTest.php` | Covered for the changed numeric behavior |
| Spatial versus representation mapping of closure/simplicity, including higher-dimensional crossings and partial/inconsistent contexts | Outside this correction | Existing preservation regressions remain applicable | Remaining R3 decision; not resolved by #64 |

This is an approved observable behavior change before 1.0.0: mixed integer/float
representations that previously compared unequal now compare equal. Existing
callers of `equalsTo()` can therefore observe different closure or simplicity
results when only numeric storage types differ. Public signatures and
concrete-class compatibility are unchanged.
