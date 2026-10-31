# Normative conformance review for 1.0.0

Audit for [Story #26](https://github.com/longitude-one/spatial-types/issues/26),
updated on 2026-10-30 against implementation baseline
`8e3c58ded02de4b335e13d4bbfb4f65549bbbe20` (merged PR #67).
The audit branch adds documentation only; its `lib/`, `tests/`, Composer,
quality configuration and workflows are identical to that baseline.

**Disposition: corrections R1–R4 are integrated for their approved scopes;
the final 1.0.0 normative baseline is still blocked by the residual predicate
findings below.** No runtime correction or implementation-status change is
made by this audit. Passing tests do not certify a complete spatial predicate.

The [previous report](audit-history/normative-conformance-review-before-predicate-integration.md)
preserves the 2026-10-16 audit and intermediate 2026-10-30 observations.
Its obsolete paths, test counts and open/merged descriptions are historical.

## Scope, sources and ownership

`SpatialTypeImplementationStatus::isFullyImplemented()` currently reports the
eleven types below. Each has immutable Geometry/Geography classes and mutable
classes with family metadata. Layouts are XY, XYZ, XYM and XYZM except
PolyhedralSurface, which supports XYZ and XYZM. Coordinate dimension here means
ordinate layout, not topological dimension.

| Reviewed type | ISO concept | Represented members or position |
| --- | --- | --- |
| Point | 4.2.3 | One declared-layout position, including the adopted all-null EMPTY representation |
| LineString | 4.2.5 | Ordered defining points with linear interpretation |
| CircularString | 4.2.6 | Ordered circular-arc control points |
| CompoundCurve | 4.2.7 | Ordered line/circular curve components |
| Polygon | 4.2.10 | Exterior boundary first, followed by interior boundaries |
| Triangle | 4.2.11 | Distinct type identity using polygon boundary storage |
| PolyhedralSurface | 4.2.12 | Polygon patches in a layout containing Z |
| GeometryCollection | 4.2.14 | Heterogeneous and nested spatial values |
| MultiPoint | 4.2.15 | Point members |
| MultiLineString | 4.2.17 | LineString members |
| MultiPolygon | 4.2.19 | Polygon members |

Excluded (`false`): BrepSolid, Circle, Clothoid, CompoundSurface,
EllipticalCurve, GeodesicString, NurbsCurve, SpiralCurve, CurvePolygon, TIN,
MultiCurve and MultiSurface. Non-instantiable model types are not reviewed as
concrete types. The inventory comes from
`lib/Implementation/SpatialTypeImplementationStatus.php` and its tests, not
from the set of currently valid constructed values.

| Source | Inspected evidence and limits |
| --- | --- |
| Primary ISO | Local **ISO/IEC CD 13249-3:201x(E)** PDF, Committee Draft dated 2009-01-16. Directly re-inspected: 4.2.1.6, relevant type concepts in 4.2.3–19, 5.1.5–9, 7.1.6–7, 7.2.1 Description 4–11, 8.5.1 Description 2–20, 9.1.1 Description 2–11, 9.2.1 Description 5, and 20.1/20.3. This is not a final published edition. |
| Supporting OGC | Supplied full transcription of **OGC 06-103r4, Simple Feature Access 1.2.1**, especially 6.1.5–7 and 6.1.12.1, inspected independently. ISO remains primary under repository arbitration. |
| ISO 19107/19111 | Full texts unavailable. Alexandre explicitly accepted this as non-blocking R5 on 2026-10-30. No direct verification or complete conformance to these sources is claimed. |
| ISO 19136 / SQL/MM completeness | Encoding/decoding and GML belong to other components; this package does not implement the complete SQL type/routine/schema surface required by ISO clause 20. |
| Project decisions | #33 externalizes functional validation; #36 permits partial-null positions; #38 defines mutable/immutable representation; #57 defines the public boundary; #59/#60/#64/#65 approve the specific predicate corrections below. |

The [validation contract](validators.md) governs package responsibility.
Normative well-formedness requirements still exist, but automatic enforcement
of ring validity, coordinate completeness, family/layout/reference compatibility,
continuity and topology belongs to explicit consumer validation. Storing an
invalid representation is not certified as valid geometry by this matrix.
Public predicates provided by this package must still have correct contracts.

## Integrated corrections and approvals

GitHub verification on 2026-10-30 found these ten completed prerequisites
of #26 closed: #20, #24, #25, #36, #38, #57, #59, #60, #64 and #65.
Parent Epic #23 and Story #26 remain open; #26 is `In review`.
Residual findings are tracked separately in open [#68](https://github.com/longitude-one/spatial-types/issues/68), [#69](https://github.com/longitude-one/spatial-types/issues/69) and [#70](https://github.com/longitude-one/spatial-types/issues/70). Completed prerequisites do not resolve those predicate contracts.

| Finding / work | Reviewed implementation evidence | Actual integration | Remote checks |
| --- | --- | --- | --- |
| R1 / [#59](https://github.com/longitude-one/spatial-types/issues/59), [PR #62](https://github.com/longitude-one/spatial-types/pull/62) | `13ccd77da0782ea38448c4413e619941ed7a2fe0`; PR handoff records independent `READY FOR MERGE` on that revision | Merged `cd49e5112867d15eba073f696f556b3cab3e6a8b`, 17:43:08 UTC | PHP 8.4, PHP 8.5 and GitGuardian successful on PR head |
| R2 / [#60](https://github.com/longitude-one/spatial-types/issues/60), [PR #63](https://github.com/longitude-one/spatial-types/pull/63) | PR handoff records independent `READY FOR MERGE` on historical `d482da5`; actual PR head is `0f80564cba7b14dfab2d8d2e0d58248214bef5bb`. Their R2 implementation/tests are identical; the intervening source difference is the separately approved R1 classification/closure change. This is a provenance distinction, not a claim that the two commit IDs are equal. | Merged `66b792a7d258c99abbed2aff59644db6c40e48e1`, 18:04:06 UTC | PHP 8.4, PHP 8.5 and GitGuardian successful on actual PR head |
| R3 representation correction / [#64](https://github.com/longitude-one/spatial-types/issues/64), [PR #66](https://github.com/longitude-one/spatial-types/pull/66) | Published independent re-review `READY FOR MERGE` on `66bea455e56617c74f4d2a2b92cabd61fb4c8d22`, superseding the precision finding on `c9f7d27` | Merged `c222b956f58bcfcedc27ea6a331616648268dd61`, 20:41:47 UTC | PHP 8.4, PHP 8.5 and GitGuardian successful on reviewed head |
| R4 / [#65](https://github.com/longitude-one/spatial-types/issues/65), [PR #67](https://github.com/longitude-one/spatial-types/pull/67) | Published independent `READY FOR MERGE` on `3e5464aa8efc3126b607554859d8be6d61826535` | Merged `8e3c58ded02de4b335e13d4bbfb4f65549bbbe20`, 21:13:23 UTC | PHP 8.4, PHP 8.5 and GitGuardian successful on reviewed head |

Approved equality is representation equality with exact numeric equivalence:
`1` and `1.0` compare equal, but `9007199254740993` and
`9007199254740992.0` do not. Null is distinct from zero; different SRIDs,
families, declared layouts and the existing concrete-class rule remain
significant. This is not an implementation of ISO spatial `ST_Equals`.
See [numeric point equality](numeric-point-equality.md).

Approved collection EMPTY is independent of stored cardinality: zero members
and all-EMPTY members report true; a non-EMPTY member makes the collection
non-EMPTY. Members, nesting and identities remain preserved. Existing
non-collection EMPTY contracts are unchanged. See
[collection emptiness](collection-emptiness.md).

## Current conformance matrix

Implementation and test paths below are repository-relative. A listed test
file identifies its complete applicable scenarios unless a method is named.
`Conformant` certifies only the stated representation or approved predicate
requirement, not every instance's validity or full SQL/MM conformance.
`Not applicable` identifies enforcement or functionality owned elsewhere.
No unapproved difference is classified as `Intentional divergence`.

| Spatial type | Requirement | Normative / adopted source | Implementation | Automated verification | Status | Limit / notes |
| --- | --- | --- | --- | --- | --- | --- |
| All eleven | Concrete identity, instantiability and declared coordinate layout | ISO 4.2.3–19; 4.2.1.6; adopted #38/#57 | `lib/Implementation/SpatialTypeImplementationStatus.php`; concrete `lib/Types/` classes and interfaces | `tests/Unit/Implementation/SpatialTypeImplementationStatusTest.php`; `tests/Unit/Types/ConcreteTypeContractTest.php::testConcreteTypesExposeTheirPublicContract()`; `tests/Unit/Types/SpatialVariantContractTest.php` | Conformant | The PHP hierarchy is not required to reproduce SQL inheritance or expose every SQL routine. |
| All eleven | Preserve declared reference and ordinates during reference-only copy/edit | ISO 5.1.5 versus 5.1.6; adopted #38 | `lib/Internal/Types/AbstractSpatialTypeImmutable.php`; mutable context setters and aggregate overrides | `tests/Unit/Types/SpatialWithSridTest.php`; `tests/Unit/Types/MutablePointSequenceContextTest.php`; `tests/Unit/Types/MutablePolygonContextTest.php`; `tests/Unit/Types/MutablePolyhedralSurfaceContextTest.php`; `tests/Unit/Types/MutableMultiObjectContextTest.php` | Conformant | Assignment is not coordinate transformation; authority metadata remains represented. |
| Point | Store X/Y/Z/M and distinguish all-null EMPTY from partial positions | ISO 4.2.3; 4.2.1.6; adopted #36 | `lib/Types/PointBehaviorTrait.php`; `lib/Value/Coordinates.php` | `tests/Unit/Types/EmptyPointTest.php`; `tests/Issue/PartiallyNullXYPointTest.php`; `tests/Issue/PartiallyNullXYZPointTest.php`; `tests/Issue/PartiallyNullXYMPointTest.php`; `tests/Issue/PartiallyNullXYZMPointTest.php` | Conformant | Storage/EMPTY mapping only. Coordinate completeness and geographic numeric ranges are external validity concerns. |
| Point | Exact representation equality without numeric storage-type bias | Adopted R3/#64; explicitly not ISO spatial equality | `lib/Types/PointBehaviorTrait.php::equalsTo()` | `tests/Issue/NumericPointEqualityTest.php`; `tests/Issue/LargeNumericPointEqualityTest.php`; `tests/Issue/NumericEqualityPredicateTest.php` | Conformant | All layouts/families/variants covered. Mixed comparison does not round integers to float; context and null distinctions remain. Wider predicate mapping remains R3-P. |
| LineString | Ordered defining positions and exactly-two-point line classification | ISO 4.2.5; 7.2.1 Description 7–8; OGC 6.1.7.1 | `lib/Internal/Types/AbstractLineStringImmutable.php`; `lib/Internal/Types/AbstractLineString.php` | `tests/Issue/LineClassificationTest.php`; `tests/Unit/Types/MutableLineStringTest.php::testMembershipEditsPreserveOrderAndIdentity()` | Conformant | R1 resolved; endpoint closure is independent of line cardinality. |
| LineString | Recognize the approved proper XY interior crossing without mutation | ISO 4.2.4; 5.1.8; OGC 6.1.6; adopted R2/#60 | `lib/Internal/Types/AbstractLineStringImmutable.php::isSimple()` | `tests/Issue/XyLineStringCrossingTest.php` | Conformant | Limited correction for complete finite homogeneous XY paths; no general simplicity certification. R3-P and residual simplicity findings remain. |
| CircularString | Preserve ordered arc control points and circular type | ISO 4.2.6; adopted #33/#38 | `lib/Internal/Types/AbstractCircularStringImmutable.php`; `lib/Internal/Types/AbstractCircularString.php` | `tests/Unit/Types/CircularStringValidationTest.php::testMultipleArcs()`; `tests/Unit/Types/MutableCircularStringTest.php::testArcEditsPreserveIdentityAndSharedEndpoints()`; `tests/Unit/Types/MutablePointSequenceContextTest.php` | Conformant | Storage and layout evidence; geometric arc validity is external. Closure mapping remains R3-P. |
| CompoundCurve | Ordered line/circular components without linearization | ISO 4.2.7; adopted #33/#38 | `lib/Internal/Types/AbstractCompoundCurveImmutable.php`; `lib/Internal/Types/AbstractCompoundCurve.php` | `tests/Unit/Types/CompoundCurveValidationTest.php::testLinearComponents()` and `testCircularComponents()`; `tests/Unit/Types/MutableCompoundCurveTest.php::testOrderedMembershipAndSharedEdits()`; `tests/Issue/PartiallyNullCompoundCurveTest.php` | Conformant | Continuity/context enforcement is external; endpoint closure mapping remains R3-P. |
| Polygon | Exterior-first boundary sequence, then stored interiors | ISO 4.2.9–10; adopted #33/#38 | `lib/Internal/Boundary/PolygonBoundary.php`; `lib/Internal/Types/AbstractPolygonImmutable.php`; `lib/Internal/Types/AbstractPolygon.php` | `tests/Unit/Types/AggregateBoundaryAndValidationTest.php`; `tests/Unit/Types/MutablePolygonTest.php::testOrderedEditingAndSharing()`; `tests/Unit/Types/MutablePolygonLayoutTest.php` | Conformant | Representation/order only. Ring/topology enforcement is not attributed to this package. |
| Triangle | Distinct Triangle identity, polygon boundary storage and retained ordinates | ISO 4.2.11; adopted #33 | Concrete Triangle classes in `lib/Types/`; polygon bases above | `tests/Unit/Types/TriangleTest.php`; `tests/Unit/Types/MutablePolygonTest.php::testEmptyAndInvalidTriangleStates()`; `tests/Unit/Types/RepresentationWithoutValidationTest.php::testXymTriangleStoresOpenBoundariesAndHoles()` | Conformant | Four-point/no-hole/non-collinearity validity is externally checked, not a constructor guarantee. |
| PolyhedralSurface | Ordered polygon patches, supported XYZ/XYZM layouts | ISO 4.2.12; 8.5.1 Description 17; adopted #33 | `lib/Internal/Types/AbstractPolyhedralSurfaceImmutable.php`; `lib/Internal/Types/AbstractPolyhedralSurface.php`; concrete XYZ/XYZM classes | `tests/Unit/Types/PolyhedralSurfaceTest.php`; `tests/Unit/Types/MutablePolyhedralSurfaceTest.php::testOrderedPatchEditing()` and `testXyzmLayout()`; `tests/Unit/Types/RepresentationWithoutValidationTest.php` surface scenarios | Conformant | Connectivity, orientation and manifold/closure validity remain external. See source difference below. |
| GeometryCollection | Heterogeneous/nested storage; acyclic mutable sharing | ISO 4.2.14; 9.1.1; adopted #33/#38 | `lib/Internal/Types/AbstractCollectionImmutable.php`; `lib/Internal/Types/AbstractSpatialCollection.php`; `lib/Types/MutableComponentGuard.php` | `tests/Unit/Types/RepresentationWithoutValidationTest.php::testMembershipDoesNotEnforceFamilyDimensionOrReference()`; `tests/Unit/Types/MutableSpatialCollectionCycleTest.php`; `tests/Issue/AllEmptyGeometryCollectionTest.php` | Conformant | Context compatibility is not automatically enforced. Cycle rejection protects the PHP ownership graph. |
| MultiPoint | Point membership with shared positions retained; approved complete repeated instance is non-simple | ISO 4.2.15; OGC 6.1.5; adopted R2/#60 | `lib/Internal/Types/AbstractMultiPointImmutable.php`; `lib/Internal/Types/AbstractMultiPoint.php` | `tests/Issue/RepeatedMultiPointInstanceTest.php`; `tests/Issue/NumericEqualityPredicateTest.php`; `tests/Issue/AllEmptyMultiPointTest.php` | Conformant | Only the stated correction/storage requirement. EMPTY simplicity and Z/M spatial mapping remain unresolved below. |
| MultiLineString | Preserve LineString members, order and EMPTY members | ISO 4.2.17; adopted #9/#65 | `lib/Internal/Types/AbstractMultiLineStringImmutable.php`; `lib/Internal/Types/AbstractMultiLineString.php` | `tests/Issue/EmptyMultiLineStringDimension2Test.php`; `tests/Issue/EmptyMultiLineStringDimension3zTest.php`; `tests/Issue/EmptyMultiLineStringDimension3mTest.php`; `tests/Issue/EmptyMultiLineStringDimension4zmTest.php`; `tests/Issue/AllEmptyMultiLineStringTest.php` | Conformant | Member well-formedness enforcement is external. No aggregate simplicity routine is claimed here. |
| MultiPolygon | Preserve Polygon members and EMPTY member positions | ISO 4.2.19; adopted #10/#65 | `lib/Internal/Types/AbstractMultiPolygonImmutable.php`; `lib/Internal/Types/AbstractMultiPolygon.php` | `tests/Issue/EmptyMultiPolygonDimension2Test.php`; `tests/Issue/EmptyMultiPolygonDimension3zTest.php`; `tests/Issue/EmptyMultiPolygonDimension3mTest.php`; `tests/Issue/EmptyMultiPolygonDimension4zmTest.php`; `tests/Issue/AllEmptyMultiPolygonTest.php` | Conformant | Disjoint interiors and boundary topology are external validity concerns. |
| Four collection types above | Zero/all-EMPTY/mixed classification, nested/shared mutations and immutable copies without member loss | ISO 5.1.7; 9.1.1 Description 6–7; adopted R4/#65 | Collection `isEmpty()` overrides and mutable collection base | Four `tests/Issue/AllEmpty*Test.php` files above; `tests/Issue/MutableCollectionEmptyTransitionTest.php`; `tests/Issue/ImmutableCollectionEmptyReplacementTest.php` | Conformant | R4 resolved for collections. Existing non-collection EMPTY contracts are not extended. |
| Curves and MultiPoint | Map public geometric predicates to XY/3D operations and partial/context behavior | ISO 4.2.1.6; 5.1.8–9; 7.1.6–7; 9.2.1 Description 5; OGC 6.1.5–7 | Existing equality-dependent closure/simplicity implementations | Preservation scenarios in `tests/Issue/XyLineStringCrossingTest.php`; diagnostic examples below | Normative ambiguity | R3-P / #70: the project mapping remains unapproved. Representation equality does not resolve it. |
| Immutable LineString | Permit coincident closing endpoints on a simple ring; detect non-proper interior overlap | ISO 4.2.4; 5.1.8; OGC 6.1.6 | `lib/Internal/Types/Collection/AbstractPointCollectionImmutable.php::isSimple()`; LineString crossing extension | `tests/Issue/XyLineStringCrossingTest.php::testDistinctClosingEndpointResultIsPreserved()` preserves the wrong ring result; no normative overlap regression exists | Non-conformant | Residual LineString simplicity finding #68; separate correction required. |
| MultiPoint | EMPTY geometries are simple under either ordinary or 3D simplicity | ISO 5.1.8–9 Description 2(a) | Immutable point-collection comparison; mutable MultiPoint pair comparison | Storage/EMPTY tests pass, but no normative regression for distinct all-EMPTY members | Non-conformant | Residual EMPTY simplicity finding #69; separate correction required. |
| All eleven | Automatic geometric validity, serialization, transformations and complete SQL routine surface | Relevant type definitions; ISO 5.1.6, serialization clauses and 20 | Explicit consumer validation / other components | Representation-without-validation tests demonstrate the boundary, not validity | Not applicable | See the ownership table below. Missing source access remains accepted R5. |

## Validity requirements assigned to explicit validation

These are normative properties of well-formed geometries. Under #33 their
automatic enforcement is **Not applicable** to `spatial-types`; they are not
silently declared satisfied by permissive constructors.

| Affected types | Requirements considered | Source / package evidence |
| --- | --- | --- |
| Point and all containing structures | Coordinate completeness, numeric geographic ranges and coherent context | ISO 4.2.3 and type rules; `tests/Unit/Types/RepresentationWithoutValidationTest.php`; partial-null tests |
| LineString | Non-EMPTY well-formed cardinality and reference consistency | ISO 7.2.1 Description 4–5; `tests/Unit/Types/MutableLineStringTest.php::testEmptyAndIncompletePathsRemainEditable()` |
| CircularString | Valid arc cardinality, control points and geometric arc structure | ISO 4.2.6; `tests/Unit/Types/RepresentationWithoutValidationTest.php::testCircularStringStoresIncompleteArcsAndEmptyMembers()` |
| CompoundCurve | Endpoint continuity and consistent component context | ISO 4.2.7; `tests/Unit/Types/RepresentationWithoutValidationTest.php::testCompoundCurveStoresDisconnectedAndEmptyComponents()` |
| Polygon | Ring closure/simplicity, planarity, hole topology and connected interior | ISO 4.2.9–10; polygon representation tests in the current matrix |
| Triangle | Four exterior points, no interior boundaries, valid non-degenerate surface | ISO 4.2.11; Triangle representation-without-validation scenario in the matrix |
| PolyhedralSurface | Connected patches, orientation, manifold/closed-surface conditions and valid patches | ISO 8.5.1 Description 2–20; surface representation-without-validation scenarios |
| GeometryCollection, MultiPoint, MultiLineString, MultiPolygon | Context compatibility and member well-formedness; relevant inter-member topology | ISO 4.2.14–19; 9.1.1 Description 10–11; heterogeneous/EMPTY storage tests |

The historical PolyhedralSurface source difference remains explicit: ISO
8.5.1 Description 6 states the simple closed-polyhedron condition, whereas OGC
6.1.12.1 describes that condition when every shared boundary has exactly two
patches. This audit does not arbitrate the validity rule or reintroduce a
constructor check; no public closed-surface predicate is provided here.

## Residual findings and decisions required

R1, the two approved R2 cases, R3 representation equality and R4 collection
EMPTY are resolved by their linked merged Stories. No decision authorizes
using representation equality as a complete spatial predicate.

| Finding | Current observed result | Expected rule / decision | Release impact |
| --- | --- | --- | --- |
| [R3-P / #70: predicate mapping](https://github.com/longitude-one/spatial-types/issues/70) | XYM endpoints with equal XY but different M are not closed; equal XY MultiPoint positions with different M are simple. A coplanar XYZ crossing is reported simple. | Decide whether each PHP predicate maps to ISO ordinary XY, explicit 3D, or a separately named representation operation. Ordinary spatial operations ignore Z/M; 3D operations consider Z, not M. Also define partial/EMPTY/incompatible-context behavior. | Blocking API/Normative Gate for the final public predicate baseline. |
| [#68: residual LineString simplicity](https://github.com/longitude-one/spatial-types/issues/68) | A square ring reports closed=true/simple=false; an interior overlapping XY path reports simple=true. | A permitted first/last endpoint contact alone does not break simplicity; interior self-overlap does. These XY cases do not require choosing XY versus 3D. | Blocking non-conformance requiring separate implementation work and normative regression coverage. |
| [#69: residual MultiPoint EMPTY simplicity](https://github.com/longitude-one/spatial-types/issues/69) | Two distinct EMPTY Points produce an EMPTY MultiPoint whose `isSimple()` is false, in both mutable and immutable variants. | ISO 5.1.8 and 5.1.9 both classify EMPTY as simple. This case does not depend on the XY/3D choice. | Blocking non-conformance requiring separate implementation work and regressions. |
| R5: unavailable supporting ISO texts | No full-text ISO 19107/19111 verification available | Explicitly accepted source limitation, not a verification claim | Non-blocking by Alexandre's decision; coverage for approved behavior remains required. |

### Reproducible current observations

The following diagnostics were executed against the stated implementation
baseline on PHP 8.5.11. The ring, overlap and EMPTY-MultiPoint cases were also
reproduced independently by the Reviewer in an isolated copy of `8e3c58d`.
They record defects/decisions, not passing normative
regressions or authorization to preserve incorrect results permanently.

```php
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;

$ring = new LineStringImmutable([[0, 0], [2, 0], [2, 2], [0, 2], [0, 0]]);
$ring->isClosed(); // true
$ring->isSimple(); // false: residual non-conformance

$overlap = new LineStringImmutable([[0, 0], [2, 0], [1, 0], [1, 1]]);
$overlap->isSimple(); // true: residual non-conformance

$emptyMembers = new MultiPointImmutable([new PointImmutable(), new PointImmutable()]);
$emptyMembers->isEmpty();  // true: approved R4
$emptyMembers->isSimple(); // false: residual non-conformance
```

```php
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable as XymLine;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable as XymPoints;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable as XyzLine;

(new XymLine([[0, 0, 1], [1, 0, 2], [0, 0, 3]]))->isClosed(); // false
(new XymPoints([[1, 2, 1], [1, 2, 2]]))->isSimple();           // true
(new XyzLine([[0, 0, 0], [2, 2, 0], [0, 2, 0], [2, 0, 0]]))->isSimple(); // true
```

The Normative Gate has concrete alternatives: map the existing methods to ISO
ordinary XY predicates; provide an explicitly approved 3D mapping where
appropriate; or document distinct representation predicates through an approved
API change. Retaining current behavior without that decision is not an approved
intentional divergence. The Technical Architect makes the final choice in #70. Stories #68/#69 track
the unambiguous simplicity corrections and missing normative regressions; their
creation is not an implementation or API/compatibility approval.

## Verification and completion boundary

The current full suite and twelve integrated correction regression files were
run on the audit branch with runtime content identical to `8e3c58d`:

- `composer test-local`: **1,299 tests / 20,385 assertions**, passing; 99.38% line coverage on PHP 8.5.11.
- Focused correction files: **166 tests / 1,848 assertions**, passing.
- `git diff --check`: passing.
- Direct ISO/OGC clause inspection and the diagnostics above were performed.

No PHP source or automated test changes belong to this audit update. The
runtime PRs' green PHP 8.4/PHP 8.5/GitGuardian checks are recorded separately
from this Markdown-only audit. `.github/workflows/ci.yml` ignores Markdown-only
PR changes; no fresh audit CI success is inferred. The existing full quality
result on the identical #65 runtime is evidence from that correction, not a new
quality execution for this documentation update.

The inventory, ownership review, current matrix, available-source assessment,
merged correction trace and missing-coverage findings are ready for independent
review. Story #26 cannot satisfy its remaining acceptance criteria for resolved
blocking findings and a stable normative baseline until the residual predicate
Gate/corrections are resolved and verified. No merge, Done transition, complete
SQL/MM conformance claim or new fully-conformant type certification is made.
