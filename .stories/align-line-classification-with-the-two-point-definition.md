# Align line classification with the two-point definition

**As a** consumer of spatial curve predicates,
**I want** mutable and immutable line strings to apply the same normative line definition,
**so that** classification does not depend on the object's mutability.

## Context

Finding R1 of the [Story #26 audit](https://github.com/longitude-one/spatial-types/issues/26):
`LineStringImmutable([[0,0],[1,1],[2,2]])->isLine()` returns `true`, while
the mutable equivalent returns `false`. The common `LineStringInterface`
documents exactly two defining points.

ISO/IEC CD 13249-3:201x(E), clauses 4.2.5 and 7.2.1 Description 8, and
OGC 06-103r4, clause 6.1.7.1 agree on the two-point definition.
The immutable closure implementation currently calls `isLine()`, so a
classification correction must preserve independent closure behavior.

This finding blocks the 1.0.0 normative baseline and public-contract
stabilization. It proposes a public behavior correction; the API/compatibility
decision must be approved before implementation. It is not Ready by creation.

## Acceptance Criteria

- [ ] `isLine()` returns true exactly when the line string has two defining point entries, for both mutable and immutable variants.
- [ ] Empty, one-point and three-or-more-point line strings return false from `isLine()`.
- [ ] Closed line strings with more than two defining points remain closed according to the separately approved closure contract.
- [ ] Classification does not mutate coordinates, members, context or the original immutable object.

## Constraints

- Apply the line definition in ISO/IEC CD 13249-3:201x(E), clauses 4.2.5 and 7.2.1 Description 8, with supporting OGC 06-103r4, clause 6.1.7.1.
- Do not reintroduce functional constructor validation removed by #33.
- Do not resolve equality, Z/M closure or EMPTY aggregate semantics from audit R3/R4 in this correction.

## Definition of Done

The project's common Definition of Done applies.

- [ ] `composer test-local` passes.
- [ ] `composer quality` passes.
- [ ] Explicit regressions cover XY, XYZ, XYM and XYZM, both families, both mutability variants, and a closed line string with more than two points.
- [ ] Public documentation and the #26 conformance matrix reflect the approved contract and final verification.
- [ ] The API and compatibility implications are approved and recorded before implementation.
