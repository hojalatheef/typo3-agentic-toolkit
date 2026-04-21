---
name: extbase-modern
description: >-
  Build Extbase plugins with the TYPO3 v13.3–v14.2 features - #[Authorize] action authorization, #[RateLimit] and the unified RateLimiterFactory, Symfony validator constraints, #[FileUpload] with PSR-7 upload validators, parameter-level #[Validate]/#[IgnoreValidation], property-based attribute configuration, automatic sys_history tracking, orderBy() expressions and argument mapping exception handling. Use when writing or reviewing an Extbase controller, model, repository or DTO for TYPO3 14, or when the user asks to "secure an action", "rate limit a form", "validate with Symfony constraints" or "handle uploads in Extbase".
argument-hint: "[controller or model path] [feature, e.g. authorize|ratelimit|upload|validate]"
allowed-tools: Read, Grep, Glob, Edit, Write
paths:
  - "**/Classes/Controller/**/*.php"
  - "**/Classes/Domain/Model/**/*.php"
  - "**/Classes/Domain/Repository/**/*.php"
  - "**/Configuration/Extbase/Persistence/Classes.php"
---

# Modern Extbase (TYPO3 v14)

Arguments: `$ARGUMENTS`. A path, if given, is the class to work on; a keyword narrows the feature.

Extbase in v14 moved most cross-cutting concerns (access control, throttling, validation, uploads, audit trail)
into declarative PHP attributes in the namespace `TYPO3\CMS\Extbase\Attribute`. Prefer these over hand-written
checks in the action body. Target TYPO3 14.3 LTS, PHP 8.2+.

## When to use

- A new or existing Extbase controller needs login/group/ownership checks: `#[Authorize]` (Feature-107826, 14.2).
- A form submit or API action needs brute-force protection: `#[RateLimit]` (Feature-108982, 14.2) and the
  `RateLimiterFactoryInterface` (Feature-109080, 14.2).
- A model needs validation beyond the Extbase built-ins: Symfony constraints (Feature-106945, 14.0).
- A model property receives file uploads: `#[FileUpload]` (Feature-103511, 13.3) and the upload validators
  (Feature-104526, 13.3).
- Code still uses `#[Validate(param: ...)]`, `#[IgnoreValidation(argumentName: ...)]`, array-style attribute
  configuration or the `TYPO3\CMS\Extbase\Annotation` namespace: migrate (Feature-108227, Feature-97559, 14.0).
- Sorting needs `CONCAT`/`TRIM`/`COALESCE`: `$query->orderBy()` (Feature-32051, 14.2).
- A detail action throws on unknown UIDs: `handleArgumentMappingExceptions` (Feature-104321, 13.3).
- Someone needs an audit trail of frontend edits: history tracking (Feature-107289, 14.2).

## Steps

1. Read the controller and its models. Note the target TYPO3 range in `composer.json`. Features marked 14.2
   need `typo3/cms-core: ^14.2`; for a 13.4 + 14 dual-line extension, use only the 13.3 features
   (uploads, upload validators, argument mapping exceptions).
2. Import attributes from `TYPO3\CMS\Extbase\Attribute` (the `Annotation` namespace is deprecated in 14.0 and
   removed in v15; annotations in docblocks no longer work in 14.0, Breaking-107229).
3. Configure attributes with named properties, not an array: `#[Validate(validator: 'NotEmpty')]`,
   `#[FileUpload(validation: [...], uploadFolder: '1:/user_upload/')]` (Feature-97559).
4. Put `#[Validate]` / `#[IgnoreValidation]` on the action parameter, not on the method with
   `param:`/`argumentName:` (Feature-108227).
5. Add `#[Authorize]` to every action that changes or reveals user-owned data; stack several when needed - all
   must pass, otherwise a 403 is propagated. Use group UIDs, not titles.
6. Add `#[RateLimit]` to uncached write actions (create, contact, login-like). It does nothing on cached actions.
7. For upload properties, use `#[FileUpload]` with `validation` + `uploadFolder`; tune it per request in
   `initialize<Action>Action()` via `getFileHandlingServiceConfiguration()`.
8. Start from [templates/ProfileController.php](templates/ProfileController.php) and
   [templates/Profile.php](templates/Profile.php), which combine the attributes.
9. Hand off to the `security-hardening` skill for a review of the finished plugin.

Details and the exact option lists: [references/attributes.md](references/attributes.md),
[references/persistence-and-errors.md](references/persistence-and-errors.md).

## Checklist

- [ ] No `TYPO3\CMS\Extbase\Annotation\*` imports and no `@Extbase\...` docblock annotations remain.
- [ ] No array as first attribute argument (deprecated, Feature-97559).
- [ ] No `param:` / `argumentName:` on method-level validation attributes (deprecated, Feature-108227).
- [ ] Every edit/update/delete action has an `#[Authorize]` with an ownership `callback`.
- [ ] Authorization callback classes are `#[Autoconfigure(public: true)]`.
- [ ] `#[RateLimit]` only on uncached actions; plugin action is registered as non-cacheable.
- [ ] `RateLimiterFactoryInterface` (TYPO3 namespace) is type-hinted, not the Symfony factory.
- [ ] `#[FileUpload]` has `fileSize`, `mimeType` and `fileExtension` validation; `addRandomSuffix` stays `true`.
- [ ] Symfony constraints avoid `#[File]` / `#[Image]` (not supported by the Extbase decorator).
- [ ] Tables with personal data have `ctrl.extbase.enableHistoryTracking => false` if the toggle is on.
- [ ] Detail actions set `showPageNotFoundIfTargetNotFoundException = 1` or override
      `handleArgumentMappingExceptions()`.

## Pitfalls

- `#[Authorize]` denies with a `PropagateResponseException` (403) before the action runs. A custom response from
  `BeforeActionAuthorizationDeniedEvent` must not be cached - only use it for uncached actions.
- `#[RateLimit]` keys on the client IP. Behind a proxy, `reverseProxyIP` must be configured or all users share
  one bucket.
- The rate limit message is a translation key without domain (`messages.rate_limit_message`) or a full
  `LLL:EXT:` reference.
- Upload validation runs only if all other properties validate; on any validation error the user must upload
  again (no temporary storage).
- Nested upload targets (`modelA.modelB.file`) are not supported.
- History tracking is off by default (feature toggle `extbase.enableHistoryTracking`) and stores full data
  snapshots - a GDPR topic.

## References

- [references/attributes.md](references/attributes.md) - `#[Authorize]`, `#[RateLimit]`, validation and
  upload attributes with every option.
- [references/persistence-and-errors.md](references/persistence-and-errors.md) - rate limiter factory, history
  tracking, `orderBy()` expressions, class configuration provider, argument mapping exceptions.
- [templates/ProfileController.php](templates/ProfileController.php),
  [templates/Profile.php](templates/Profile.php),
  [templates/ProfileAuthorization.php](templates/ProfileAuthorization.php) - copy-ready files.
- Changelog: Feature-107826, Feature-108982, Feature-109080, Feature-107289, Feature-106945, Feature-103511,
  Feature-104526, Feature-108227, Feature-97559, Feature-108975, Feature-32051, Feature-104321,
  Breaking-107229, Deprecation-107229.
