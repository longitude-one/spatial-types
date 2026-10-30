# Normative conformance review for 1.0.0

## Resumption on 2026-10-30

The current implementation baseline is
`dbd6d1c` (the merge of public/internal API Story #57). The audit branch
`dfae978` adds only the historical report below. This resumption is an audit;
it changes no runtime behavior, supported API or implementation-status result.

**Disposition: the audit remains incomplete and blocks contract stabilization.**
The historical matrix below must not be used as a current conformance claim.
In particular, its `Validator/` paths and automatic rejection assertions no
longer describe this package.

### Approved changes since the original review

At the start of resumption, GitHub reported the six existing blocking
dependencies of #26 closed: #20, #24, #25, #36, #38 and #57. Story #26 was
`In progress`; its parent Epic #23 was open.
No pull request existed for this audit branch at the start of resumption.

| Approved work | Consequence for this review |
| --- | --- |
| [#33](https://github.com/longitude-one/spatial-types/issues/33) | Functional validation, including equivalent inline checks, was removed. `spatial-types` does not invoke or depend on `spatial-validator`. |
| [#36](https://github.com/longitude-one/spatial-types/issues/36) | Partial-null positions are representable; only all-null point ordinates mean EMPTY. This is a representation contract, not proof of normative validity. |
| [#38](https://github.com/longitude-one/spatial-types/issues/38) and #39–#46 | Both mutable and immutable representations are now in scope. Immutable concrete names end in `Immutable`; mutable classes live directly under each dimension namespace. |
| [#55](https://github.com/longitude-one/spatial-types/issues/55) | Public factories/builders add construction paths; they do not establish geometric validity. |
| [#57](https://github.com/longitude-one/spatial-types/issues/57) | Shared abstract implementations now live under `Internal/`. Public interfaces and concrete types remain the audit targets. |

The repository's current [validation contract](validators.md) is authoritative
for ownership. Requirements for geometric validity remain normative, but their
automatic enforcement is outside this package under #33. Conversely, a public
predicate exposed by this package still needs a correct, explicit contract.
Moving validators does not establish correctness of `isSimple()` or `isClosed()`.

### Current inventory and evidence

`SpatialTypeImplementationStatus::isFullyImplemented()` still reports eleven
types. The scope includes immutable Geometry and Geography variants and mutable
variants with either family as instance metadata. The applicable layouts are
XY, XYZ, XYM and XYZM, except PolyhedralSurface has XYZ and XYZM only.
The twelve unsupported types listed in the historical scope remain excluded.

The following matrix records the **representation boundary**, not an assertion
that every represented value satisfies the corresponding geometry definition.
Implementation paths are relative to `lib/`; a bare implementation filename
is under `Internal/Types/`. Test paths are relative to `tests/`; a bare test
filename or `Dimension*` test path is under `Unit/Types/`. Braces enumerate
the named variants, including an empty alternative for the mutable base.

| Spatial type | Reviewed requirement / ownership | Normative reference | Current implementation | Automated evidence | Status and remaining limit |
| --- | --- | --- | --- | --- | --- |
| All eleven | Concrete identity, instantiability and fixed coordinate layout | ISO 4.2.3–19; 4.2.1.6 | `Implementation/SpatialTypeImplementationStatus.php`; `Types/Dimension*`; common interfaces | `Unit/Implementation/SpatialTypeImplementationStatusTest.php`; `Unit/Types/ConcreteTypeContractTest.php`; `SpatialVariantContractTest.php` | Conformant for the stated type/layout inventory. This does not certify functional validity. |
| All eleven | SRS assignment retains ordinates; transformation is external | ISO 5.1.5 versus 5.1.6 | `Internal/Types/AbstractSpatialTypeImmutable.php`; concrete mutable context methods | `Unit/Types/SpatialWithSridTest.php`; `MutablePointSequenceContextTest.php`; `MutablePolygonContextTest.php`; `MutablePolyhedralSurfaceContextTest.php`; `MutableMultiObjectContextTest.php` | Conformant for declared-reference replacement/propagation. Assignment does not transform coordinates. |
| Point | Position storage, Z/M preservation and EMPTY representation | ISO 4.2.3; 4.2.1.6; 6.1.1 | `Types/PointBehaviorTrait.php`; `Value/Coordinates.php`; `Internal/Types/AbstractPoint{,Immutable}.php` | `Unit/Types/EmptyPointTest.php`; `Issue/PartiallyNull{XY,XYZ,XYM,XYZM}PointTest.php`; `Unit/Types/Dimension{2,3z,3m,4zm}/PointTest.php` | Conformant for storage under #36. Coordinate completeness enforcement: Not applicable to this package. |
| LineString | Ordered defining points; line classification | ISO 4.2.5; 7.2.1, Description 7–8; OGC 6.1.7.1 | `Internal/Types/AbstractLineString{,Immutable}.php` | `Unit/Types/MutableLineStringTest.php`; `Dimension2/Geometry/LineStringTest.php` | Non-conformant: immutable `isLine()` accepts more than two points; mutable does not (R1). Existing immutable tests preserve the defect. |
| CircularString | Ordered arc control points; retain circular representation | ISO 4.2.6; 7.3.1 | `Internal/Types/AbstractCircularString{,Immutable}.php` | `Unit/Types/CircularStringValidationTest.php`; `MutableCircularStringTest.php`; dimension/family CircularString tests | Conformant for control-point storage. Odd cardinality and valid arcs are explicit validation concerns under #33. |
| CompoundCurve | Ordered line/circular component representation; no linearization | ISO 4.2.7; 7.4.1 | `Internal/Types/AbstractCompoundCurve{,Immutable}.php` | `Unit/Types/CompoundCurveValidationTest.php`; `MutableCompoundCurveTest.php`; `Issue/PartiallyNullCompoundCurveTest.php` | Conformant for ordered supported component storage. Continuity and compatible context enforcement: Not applicable here. |
| Polygon | Exterior first, then interior boundary sequences | ISO 4.2.10; 8.3.1 | `Internal/Boundary/PolygonBoundary.php`; `Internal/Types/AbstractPolygon{,Immutable}.php` | `Unit/Types/AggregateBoundaryAndValidationTest.php`; `MutablePolygonTest.php`; `MutablePolygonLayoutTest.php` | Conformant for storage/order. Closure, simplicity, planarity and hole topology enforcement: Not applicable here. |
| Triangle | Distinct triangle identity and polygon boundary representation | ISO 4.2.11; 8.4.1; OGC 6.1.11.1 | `Types/Dimension*/Triangle.php`; `Types/Dimension*/{Geometry,Geography}/TriangleImmutable.php` | `Unit/Types/TriangleTest.php`; `MutablePolygonLayoutTest.php`; `RepresentationWithoutValidationTest.php::testXymTriangleStoresOpenBoundariesAndHoles()` | Conformant for identity/storage. Cardinality, holes and non-collinearity enforcement: Not applicable here. |
| PolyhedralSurface | Ordered polygon patch storage in layouts containing Z | ISO 4.2.12; 8.5.1 | `Internal/Types/AbstractPolyhedralSurface{,Immutable}.php`; XYZ/XYZM concrete types | `Unit/Types/PolyhedralSurfaceTest.php`; `MutablePolyhedralSurfaceTest.php`; `RepresentationWithoutValidationTest.php` surface cases | Conformant for patch representation. Connectivity, planarity, manifold and closed-surface validation are external. Historical F4 remains a source conflict for validation, not justification to reject representations here. |
| GeometryCollection | Heterogeneous and nested spatial member storage | ISO 4.2.14; 9.1.1 | `Internal/Types/AbstractCollectionImmutable.php`; `AbstractSpatialCollection.php` | `Issue/Issue1/CollectionTest.php`; `Unit/Types/RepresentationWithoutValidationTest.php::testMembershipDoesNotEnforceFamilyDimensionOrReference()`; `MutableMultiObjectTest.php`; `MutableSpatialCollectionCycleTest.php` | Conformant for storage. Historical mixed-layout/family rejection F6 is superseded by #33. Cycle rejection protects the PHP ownership graph. |
| MultiPoint | Point membership and geometric simplicity | ISO 4.2.15; 5.1.8; 9.2.1; OGC 6.1.5 | `Internal/Types/Collection/AbstractPointCollectionImmutable.php`; `AbstractMultiPoint.php`; `Types/PointBehaviorTrait.php` | `Issue/EmptyMultiPointMembersTest.php`; `Unit/Types/MutableMultiObjectTest.php::testSimplicity()`; dimension/family MultiPoint tests | Conformant for member storage; Non-conformant for immutable repeated-instance simplicity (R2). Numeric/Z/M/class-dependent equality needs a predicate mapping decision (R3). |
| MultiLineString | LineString member representation; preserve EMPTY members | ISO 4.2.17; 9.4.1 | `Internal/Types/AbstractMultiLineString{,Immutable}.php` | `Issue/EmptyMultiLineStringDimension{2,3z,3m,4zm}Test.php`; `Unit/Types/MutableMultiObjectTest.php` | Conformant for storage. Member well-formedness enforcement: Not applicable here. |
| MultiPolygon | Polygon member representation; preserve EMPTY members | ISO 4.2.19; 9.6.1 | `Internal/Types/AbstractMultiPolygon{,Immutable}.php` | `Issue/EmptyMultiPolygonDimension{2,3z,3m,4zm}Test.php`; `Unit/Types/MutableMultiObjectTest.php` | Conformant for storage. Disjoint interiors and permitted boundary contacts are external validation concerns. |
| Curves, MultiPoint | Correct public geometric predicates | ISO 4.2.1.6; 5.1.8; 7.1.6–7; OGC 6.1.5–7 | Equality trait and curve/multi-point bases above | Existing tests cover ordinary cases; complete normative regressions are missing | Normative ambiguity about mapping the PHP predicates to ISO XY/3D operations; R2/R3 prevent a conformance claim. |
| Aggregates | EMPTY set versus zero stored members | ISO 5.1.7; 9.1.1, Description 6–7 | Aggregate `isEmpty()` implementations | EMPTY-member tests retain members and assert cardinality-based non-emptiness | Normative ambiguity: R4. Approved storage preservation alone does not determine the public predicate. |
| All eleven | Encoding/decoding, transformations, SQL routine completeness | ISO 5.1.6, 5.1.46–58; clause 20; ISO 19136 | Other components | No package-local verification required | Not applicable. No full SQL/MM implementation conformance is claimed. |

### Reproduced findings and decisions required

The ISO clauses below were re-inspected directly in the local
**ISO/IEC CD 13249-3:201x(E)** PDF, dated 2009-01-16. The supplied OGC
06-103r4 transcription was consulted independently for clauses 6.1.5–7
and 6.1.12.1. Neither source is inferred from database behavior.

The following observations were executed against `dfae978` on 2026-10-30.
They are diagnostics, not newly introduced tests certifying current behavior.
Each finding blocks this audit's final baseline; no runtime fix is included.

| ID | Reproduction / observed result | Expected behavior or decision |
| --- | --- | --- |
| R1 | Three-point `LineStringImmutable([[0,0],[1,1],[2,2]])->isLine()` is `true`; the mutable equivalent is `false`. | ISO 4.2.5 and 7.2.1 Description 8, and OGC 6.1.7.1 agree on exactly two points. A separate correction must preserve closure as an independent endpoint predicate; simply narrowing `isLine()` would also break immutable `isClosed()`. No normative conflict was found for line cardinality. |
| R2 | `MultiPointImmutable([$p, $p])->isSimple()` is `true` for `$p = new PointImmutable(1,2)`; mutable returns `false`. Immutable crossing `[[0,0],[2,2],[0,2],[2,0]]` LineString reports simple. | Repeated member positions count even when the same object is reused. A curve's simplicity requires segment reasoning, not only distinct stored point objects (ISO 5.1.8; OGC 6.1.5–6). Correction and normative regression tests must be tracked separately. Mutable LineString does not expose `isSimple()`. |
| R3 | `PointImmutable(1,2)->equalsTo(new PointImmutable(1.0,2.0))` is `false`. Equality also considers concrete PHP class, family, dimension, SRID and every Z/M ordinate; curve closure and MultiPoint simplicity delegate to it. | API/Normative Gate: decide whether `equalsTo()` is representation equality or spatial equality, and explicitly map each dependent predicate. ISO 4.2.1.6 and 5.1.8 ignore Z/M for ordinary spatial operations; 7.1.6 ignores Z while 7.1.7 provides a separate 3D closure. Preserve numeric equivalence and define behavior on partial/EMPTY positions and differing contexts. |
| R4 | A mutable or immutable MultiPoint containing only one EMPTY Point reports `isEmpty() === false`. Other aggregate implementations also use stored-member count. | API/Normative Gate: define EMPTY for represented aggregates versus geometric empty-set semantics (ISO 5.1.7), including all-EMPTY nested members and invalid structures. Retaining EMPTY members is compatible with either a separate cardinality operation or a geometric predicate; the approved storage contract does not settle this choice. |
| R5 | Direct full-text ISO 19107/19111 evidence and complete per-layout/per-family predicate regressions are still absent. | Record the limits of the available sources. Do not claim an exhaustive normative baseline until delegated requirements are verified and the predicate regressions for the chosen contracts exist. |

R1 is tracked by [Story #59](https://github.com/longitude-one/spatial-types/issues/59);
R2 is tracked by [Story #60](https://github.com/longitude-one/spatial-types/issues/60).
On 2026-10-30, Alexandre approved exact-two-point line classification and
false for the two exact R2 cases above. Their corrections are proposed below,
but are not integrated into the audited main-branch baseline. R3 and R4 still
require Alexandre's decision for the wider predicate contracts.
No existing behavior is reclassified as an intentional divergence.

### Approved correction evidence before integration

These implementation PRs are separate from the audit. Their runtime changes
have not been merged; R1/R2 remain open against the audited main-branch
baseline. This records the proposed behavior and verification required by
Stories #59/#60 without declaring either Story Done.

| Finding | Approved behavior and implementation | Test evidence | Review and remote checks | Integration status |
| --- | --- | --- | --- | --- |
| R1 / #59 | [PR #62](https://github.com/longitude-one/spatial-types/pull/62), `13ccd77da0782ea38448c4413e619941ed7a2fe0`: `AbstractLineStringImmutable::isLine()` requires exactly two entries, matching the mutable type. `isClosed()` checks cardinality independently, so closed paths with more than two points stay closed. | `tests/Issue/LineClassificationTest.php`: 17 explicit tests across XY/XYZ/XYM/XYZM, both families and variants; updated existing immutable XY expectations. Developer full suite: 1,150 tests / 18,767 assertions; `composer quality` passes. Restoring old classification causes 9 regression failures. | Independent full suite isolated on `13ccd77`: 1,150 tests / 18,765 assertions; code, tests and compatibility favorable. PHP 8.4, PHP 8.5 and GitGuardian checks pass. Independent verdict: `READY FOR MERGE` on `13ccd77`, after verification of this trace in audit commit `66ac0c8`. | Proposed correction, not integrated; not Done. No R3/R4 interpretation changed. |
| R2 / #60 | [PR #63](https://github.com/longitude-one/spatial-types/pull/63), `d482da593f18f3ddaa786c4b1599c5c94d9a6b64`: immutable MultiPoint counts repeated complete non-empty Point instances; immutable LineString detects proper interior crossings in complete finite homogeneous XY paths, with positive per-axis normalization before determinant calculations. Floating-point precision limits remain documented. | `tests/Issue/RepeatedMultiPointInstanceTest.php` and `XyLineStringCrossingTest.php`: 32 tests / 141 assertions, covering exact cases, four-layout shared-instance behavior, preservation of unresolved cases and the review crossings scaled by `1e200`/`1e-200`. Developer full suite: 1,165 tests / 18,678 assertions; `composer quality` passes. The initial 29 regressions produce 12 failures with the original sources; both scale regressions fail without normalization. | Independent re-review on isolated `d482da5`: 1,165 tests / 18,676 assertions; ordinary and both scaled crossings return false. PHP 8.4, PHP 8.5 and GitGuardian checks pass. Consult the implementation PR for the final verdict after audit-trace verification. | Proposed correction, not integrated; not Done. Higher-dimensional intersections, partial/EMPTY, inconsistent-context and broader equality behavior remain pending R3/R4. |

### Disposition of historical findings

| Historical ID | Current disposition |
| --- | --- |
| F1, F2, F3, F5 | Automatic geometric rejection is outside this package under #33. No equivalent checks may be reintroduced by this audit. Their validation requirements remain relevant to `spatial-validator`. |
| F4 | Surface validity enforcement is external under #33; the ISO/OGC closure difference is preserved as historical normative evidence, not silently arbitrated. |
| F6 | Functional family/layout compatibility rejection was removed under #33; mixed immutable membership is now explicitly tested. |
| F7 | Still open as R4; the public EMPTY contract remains relevant. |
| F8 | Still open as R2/R3. The mutable and immutable implementations now differ. |
| F9 | Continuity enforcement is external; the public equality/closure mapping remains open as R3. |
| F10 | Still open as R5. |

## Historical audit snapshot — 2026-10-16

The following material is retained for traceability against its original
baseline. Its implementation paths, validation ownership and test mappings
are historical and are superseded by the resumption assessment above.

Audit for [Story #26](https://github.com/longitude-one/spatial-types/issues/26),
2026-10-16, baseline `ceaee5d142c77fc3922ab50c762fa6648fea0e30`.
**Result: blocking findings remain; this is not a release conformance claim.**
No runtime behavior or implementation-status classification is changed by this audit.

## Scope and sources

The [implementation-status API](../lib/Implementation/SpatialTypeImplementationStatus.php)
reports eleven implemented types: Point, LineString, CircularString, CompoundCurve,
Polygon, Triangle, PolyhedralSurface, GeometryCollection, MultiPoint,
MultiLineString and MultiPolygon. Both Geometry and Geography are reviewed;
XY, XYZ, XYM and XYZM apply, except PolyhedralSurface requires XYZ or XYZM.
Stories #24 and #25 are closed; parent Epic #23 remains open.

Excluded types (`false`): BrepSolid, Circle, Clothoid, CompoundSurface,
EllipticalCurve, GeodesicString, NurbsCurve, SpiralCurve, CurvePolygon, TIN,
MultiCurve and MultiSurface. Abstract model types are not instantiable.

**ISO** below means the locally inspected **ISO/IEC CD 13249-3:201x(E)**,
Committee Draft dated 2009-01-16, not a final published edition.
**OGC** means Simple Feature Access 1.2.1, OGC 06-103r4, inspected through
the supplied full transcription. ISO is primary; OGC supplements it without
silently resolving conflicts. ISO 19107 and ISO 19111 are normative dependencies
(ISO clause 2); their full texts were unavailable for direct verification.
ISO 19136/GML representation belongs to encoding/decoding, outside this audit.

## Conformance matrix

Implementation paths below are relative to `lib/`; test paths start at the
repository root (a bare test filename or dimension path is under
`tests/Unit/Types/`). Braces denote the listed path variants. Shared rows apply to every
listed type; a conformant row certifies only its stated requirement.
`Non-conformant` includes missing enforcement or missing normative regression
coverage. `Normative ambiguity` requires a Technical Architect decision.
No unapproved difference is classified as `Intentional divergence`.

| Spatial type | Requirement | Normative source | Implementation | Test coverage | Status | Notes |
| --- | --- | --- | --- | --- | --- | --- |
| All eleven | Concrete type identity and instantiability | ISO 4.2.3–19; concrete type definitions | `Types/Dimension*/{Geometry,Geography}/*`; `Implementation/SpatialTypeImplementationStatus` | `tests/Unit/Implementation/SpatialTypeImplementationStatusTest.php`; `tests/Unit/Types/ConcreteTypeContractTest.php` | Conformant | Enum identity is checked; PHP inheritance need not reproduce SQL inheritance. |
| All eleven | Distinct XY, XYZ, XYM, XYZM coordinate layouts; Z differs from M | ISO 4.2.1.6; 5.1.3, 5.1.11–12 | `Types/Dimension*`; `Value/Coordinates.php`; `Validator/DimensionValidation.php` | `tests/Unit/Types/ConcreteTypeContractTest.php`; `Dimension3m/XymContractTest.php`; `tests/Unit/Value/CoordinatesTest.php` | Conformant | PolyhedralSurface is restricted to layouts containing Z (ISO 8.5.1, rule 17). |
| All aggregates | Members share the parent's SRS; retain authority identity | ISO 4.2.14; 7.2.1, 7.3.1, 7.4.1, 8.2.1, 8.5.1, 9.1.1 | `Validator/SpatialReferenceValidation.php`; `Reference/SpatialReference.php` | `tests/Unit/Reference/SpatialReferenceTest.php`; `tests/Unit/Types/SpatialWithSridTest.php`; curve compatibility tests | Conformant | Authority equality and SRID 0 as a concrete reference are documented project rules. |
| All eleven | Reference assignment does not transform coordinates | ISO 5.1.5 versus 5.1.6 | `Types/AbstractSpatialType.php::withSpatialReference()` and aggregate overrides | `tests/Unit/Types/SpatialWithSridTest.php`; curve and surface reference-copy tests | Conformant | Coordinate transformation remains external. |
| Point | One position; all-null ordinates represent EMPTY; partial-null positions rejected | ISO 4.2.3; 6.1.1 | `Types/AbstractPoint.php`; concrete point constructors | `tests/Unit/Types/EmptyPointTest.php`; dimension-specific Point tests | Conformant | Geometry and Geography coordinate policies remain distinct. |
| LineString | Ordered defining positions and linear segments; no consecutive duplicates | ISO 4.2.5; 7.2.1; 7.2.3 | `Types/AbstractLineString.php`; `Validator/LineStringValidation.php` | `tests/Unit/Types/Dimension2/Geometry/LineStringTest.php::testConstructorRejectsConsecutiveDuplicatePoints()`; replacement tests | Conformant | Non-simple curves may be represented; that does not excuse an incorrect simplicity predicate. |
| LineString | Non-empty sequence contains at least two points | ISO 7.2.1, Description 4, 10–11 | `Types/AbstractLineString.php::addPoints()` | Missing rejection regression | Non-conformant | F1: one-point line accepted. |
| LineString | Simplicity considers segment intersections, not just repeated vertices | ISO 4.2.4; 5.1.8 | `Types/Collection/AbstractPointCollection.php::isSimple()` | Optional validator tests exist in `tests/Unit/Validator/Constraints/SimpleLineStringValidatorTest.php`; direct-method crossing regression missing | Non-conformant | F8: inherited predicate returns true for crossing segments. |
| LineString, CircularString, CompoundCurve | Endpoints, closure, Z/M and numeric equality | ISO 4.2.4; 4.2.1.6; 7.1.4–8; 7.3.7–8; 7.4.6–7 | `Types/Abstract{LineString,CircularString,CompoundCurve}.php`; `AbstractPoint::equalsTo()` | `tests/Unit/Types/CircularStringValidationTest.php`; `CompoundCurveValidationTest.php::testIntegerFloatMismatch()` and measure/elevation tests | Normative ambiguity | F9: current closure/continuity uses all ordinates and strict PHP numeric types; distinguish ISO XY closure, XYZ closure and spatial equality. |
| CircularString | EMPTY or odd count >=3; ordered arc control points; non-coincident intermediate points | ISO 4.2.6; 7.3.1, Description 4–12 | `Types/AbstractCircularString.php` | `tests/Unit/Types/CircularStringValidationTest.php`; eight dimension/family CircularString tests | Conformant | Collinear arcs and complete circles retained without linearization. No circular simplicity predicate is claimed. |
| CompoundCurve | Ordered LineString/CircularString components; contiguous endpoints; EMPTY compound | ISO 4.2.7; 7.4.1; 7.4.6–7 | `Types/AbstractCompoundCurve.php` | `tests/Unit/Types/CompoundCurveValidationTest.php`; eight dimension/family CompoundCurve tests | Conformant | Structure is represented; equality interpretation remains F9. Empty components and nested compounds are rejected under Story #25. |
| Polygon | Exterior first, then interior linear boundaries; closure; coordinate/reference compatibility | ISO 4.2.10; 8.3.1; 8.2.1 | `Types/AbstractPolygon.php`; `Boundary/PolygonBoundary.php`; `Validator/RingValidation.php` | `tests/Unit/Validator/Constraints/RingValidatorTest.php`; `tests/Unit/Types/AggregateBoundaryAndValidationTest.php` | Conformant | This row concerns storage and closure only. |
| Polygon, Triangle, PolyhedralSurface patches | Simple rings, planar patches, valid holes, no crossing rings/spikes; connected interior | ISO 4.2.8–11; 8.2.1, rules 2–3, 8–14; OGC 6.1.11.1 | `Types/AbstractPolygon.php`; `Validator/Constraints/RingValidator.php` | Polygon rejection tests missing for self-crossing rings, outside/crossing holes, disconnected interiors and non-planar XYZ/XYZM polygons | Non-conformant | F2: structural Ring validation omits polygon topology; PolyhedralSurface adds planarity/simplicity but not full patch topology. |
| Triangle | EMPTY or one four-position closed exterior ring, without holes | ISO 4.2.11; 8.4.1 | `Types/AbstractTriangle.php`; `Validator/Constraints/TriangleValidator.php` | `tests/Unit/Types/TriangleTest.php`; `tests/Unit/Validator/Constraints/TriangleValidatorTest.php` | Conformant | All four layouts and both families covered for structural rules. |
| Triangle | Three distinct, non-collinear vertices | OGC 6.1.11.1 versus ISO 4.2.11 and inherited polygon rules | `Validator/Constraints/TriangleValidator.php` | Non-collinearity rejection regression missing | Normative ambiguity | F5: collinear triangle accepted; approve application of the supporting OGC requirement before correction. |
| PolyhedralSurface | Connected planar 3D patches, common edges incident to at most two faces, opposite traversal | ISO 4.2.12; 8.5.1, rules 2, 4, 8, 17–19; OGC 6.1.12.1 | `Validator/Constraints/PolyhedralSurfaceValidator.php`; `Validator/Internal/PolyhedralSurfaceTopology.php` | `tests/Unit/Types/PolyhedralSurfaceTest.php`; `tests/Unit/Validator/Constraints/PolyhedralSurfaceValidatorTest.php` | Conformant | Exact XYZ arithmetic; M ignored. Patch-interior intersections and vertex-manifold coverage remain incomplete (F2/F4). |
| PolyhedralSurface | Whether free edges/open surfaces are permitted; simple sphere topology | ISO 8.5.1, rules 6–7, versus OGC 6.1.12.1 | `Validator/Internal/PolyhedralSurfaceTopology.php` | `tests/Unit/Types/PolyhedralSurfaceTest.php::testOpenSurfaceAndIndependentCopies()` | Normative ambiguity | F4: ISO wording unconditionally says closed sphere; OGC makes closure conditional. No arbitration invented here. |
| GeometryCollection | Zero or more heterogeneous spatial types, nested collections, common SRS | ISO 4.2.14; 9.1.1 | `Types/AbstractCollection.php` | `tests/Issue/Issue1/CollectionTest.php`; `tests/Unit/Types/ConcreteTypeContractTest.php` | Conformant | Heterogeneous geometry types, rather than coordinate layouts, are covered by this row. |
| GeometryCollection | Restrictions on mixed coordinate layouts/families | ISO 4.2.14; 9.1.1, rule 8 | `Types/AbstractCollection.php::addElement()` | Collection dimension/family rejection tests; no approved divergence evidence located | Normative ambiguity | F6: model restrictions exceed the general collection definition; decide mapped subtype versus intentional restriction. |
| MultiPoint | Point-only membership, no geometric ordering requirement; EMPTY members preserved | ISO 4.2.15; 9.2.1 | `Types/AbstractMultiPoint.php`; `Collection/AbstractPointCollection.php` | `tests/Issue/EmptyMultiPointMembersTest.php` | Conformant | Array order is a storage choice; it does not assert geometric order. Duplicate points are allowed, but affect simplicity. |
| MultiPoint | Simplicity detects coincident positions, ignores Z/M, independent of PHP identity | ISO 9.2.1, Description 5; 5.1.8 | `Types/Collection/AbstractPointCollection.php::isSimple()` | Same-instance, numeric-equivalence and differing-Z/M normative regressions missing | Non-conformant | F8: repeated same point object is incorrectly simple. |
| MultiLineString | LineString-only members; crossings allowed; non-empty member invariants | ISO 4.2.16–17; 9.3.1; 9.4.1 | `Types/AbstractMultiLineString.php`; `Collection/AbstractLineStringCollection.php` | `tests/Issue/EmptyMultiLineStringValidationTest.php`; dimension-specific MultiLineString tests | Non-conformant | Storage/type checks covered; one-point member defect inherited from F1. No multi-curve simplicity predicate is claimed. |
| MultiPolygon | Polygon-only members; EMPTY members preserved | ISO 4.2.19; 9.6.1 | `Types/AbstractMultiPolygon.php` | `tests/Issue/EmptyMultiPolygonDimension{2,3m,3z,4zm}Test.php` | Conformant | Membership storage only; validity is below. |
| MultiPolygon | Disjoint polygon interiors; coplanar boundaries intersect only at finitely many points; no spikes | ISO 9.6.1, Description 3–8; OGC 6.1.14 | `Types/AbstractMultiPolygon.php::addPolygon()` | Overlap, shared-edge and inherited invalid-polygon rejection regressions missing | Non-conformant | F3: even duplicate polygons are accepted. |
| MultiPoint, MultiLineString, MultiPolygon, GeometryCollection | Geometric EMPTY versus zero stored members | ISO 5.1.7; 9.1.1, Description 6–7 | Aggregate `isEmpty()` methods | Empty-member regressions assert non-empty containers, including `EmptyMultiPointMembersTest.php::testDimension2GeometryPreservesEmptyMembers()` | Normative ambiguity | F7: approve cardinality-versus-point-set interpretation; preserving empty members does not settle this predicate. |
| All eleven | WKT/WKB/GML, vendor formats, decoding and coordinate transformation algorithms | ISO 5.1.6, 5.1.46–58; ISO 19136 | External encoders/decoders/transformers | Not required in this library | Not applicable | Spatial domain invariants remain in scope; SQL routine completeness is not claimed (ISO clause 20). |

## Findings and release disposition

Every finding below blocks the 1.0.0 normative baseline until resolved or
explicitly arbitrated. Existing passing tests do not waive any finding.
Follow-up corrections must cover constructors and immutable replacements in
both families and each applicable layout, using explicit normative regressions.

| ID | Observed behavior / missing evidence | Expected result or decision required |
| --- | --- | --- |
| F1 | One-point LineString accepted: `[[0, 0]]`. | Reject malformed non-empty sequences; test direct construction, replacement and MultiLineString members. |
| F2 | Bow-tie Polygon accepted: `[[[0,0],[2,2],[0,2],[2,0],[0,0]]]`; no full polygon topology validation. | Enforce simple planar boundaries, containment, permitted contacts and connected interiors, including inherited types; add missing topology regressions. |
| F3 | MultiPolygon accepts two identical Polygon objects. | Reject intersecting interiors and shared boundary intervals; test point contacts and EMPTY members separately. |
| F4 | Open PolyhedralSurface accepted; complete patch-intersection/manifold evidence absent. | Normative Gate: reconcile ISO rule 6 with rule 7 and OGC; approve open-surface policy and residual topology obligations before changes. |
| F5 | Collinear Triangle accepted: `[[[0,0],[1,0],[2,0],[0,0]]]`. | Normative Gate: adopt supporting OGC non-collinearity requirement or document a justified approved divergence; add corresponding tests. |
| F6 | GeometryCollection rejects mixed layouts/families beyond common SRS. | Architecture/Normative Gate: approve the mapping/restriction with rationale, or plan compatible correction before stabilization. |
| F7 | A container with only EMPTY members reports false from `isEmpty()`. | Normative Gate: arbitrate point-set semantics versus storage cardinality, accounting for approved empty-member Stories #8–10. |
| F8 | Crossing LineString and `MultiPoint([$p, $p])` both report simple. | Correct public predicates against geometric positions, intersections and applicable Z/M rules; optional validators do not repair the methods. |
| F9 | Curve closure/continuity compares M and strict integer/float identity. | Normative/API Gate: define whether these public methods map to ISO XY, XYZ or another approved domain predicate; retain Story #25 behavior until approved. |
| F10 | Direct ISO 19107/19111 verification and complete per-invariant, per-layout rejection evidence unavailable. | Obtain supporting normative evidence where ISO delegates requirements; close remaining coverage gaps before claiming an exhaustive baseline. |

No behavioral correction is included. The audit is suitable for refinement
of follow-up findings, **not yet for public-contract stabilization**.
