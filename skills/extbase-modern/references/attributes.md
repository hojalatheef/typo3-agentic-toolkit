# Extbase attributes in TYPO3 v14

All attributes live in `TYPO3\CMS\Extbase\Attribute` since 14.0 (Deprecation-107229). The old
`TYPO3\CMS\Extbase\Annotation` namespace still works until v15 but is deprecated; docblock annotations were
removed (Breaking-107229).

## Authorize (Feature-107826, 14.2)

`#[Authorize]` on an action method. Options, combinable in one attribute or stacked as several attributes
