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
