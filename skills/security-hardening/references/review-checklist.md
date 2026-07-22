# Extension security review checklist (TYPO3 v14)

Work read-only. For each finding report `path:line`, the risk, the fix and the changelog or API to use. Order by
severity: critical (exploitable without login), high, medium, low.

## Input, output and queries

- [ ] SQL only via `QueryBuilder` with `createNamedParameter()` or Extbase queries; no string concatenation into
      `where()`, `orderBy()` or `executeQuery()`. Extbase `orderBy()` expressions (Feature-32051) take property
      names, not user input.
