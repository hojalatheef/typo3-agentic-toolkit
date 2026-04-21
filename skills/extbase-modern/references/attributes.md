# Extbase attributes in TYPO3 v14

All attributes live in `TYPO3\CMS\Extbase\Attribute` since 14.0 (Deprecation-107229). The old
`TYPO3\CMS\Extbase\Annotation` namespace still works until v15 but is deprecated; docblock annotations were
removed (Breaking-107229).

## Authorize (Feature-107826, 14.2)

`#[Authorize]` on an action method. Options, combinable in one attribute or stacked as several attributes
(all must pass):

| Option | Type | Meaning |
| --- | --- | --- |
| `requireLogin` | `bool` | A frontend user must be logged in. |
| `requireGroups` | `array` | User must be in at least one of the groups (OR). UIDs recommended; titles work but editors can rename them. |
| `callback` | `string` or `array` | `'methodName'` (public method on the controller) or `[SomeClass::class, 'method']`. Returns `bool`. Receives the action arguments. |

```php
#[Authorize(requireLogin: true)]
#[Authorize(requireGroups: [1, 2])]
#[Authorize(callback: [MyObjectAuthorization::class, 'checkOwnership'])]
public function editAction(MyObject $myObject): ResponseInterface
```
