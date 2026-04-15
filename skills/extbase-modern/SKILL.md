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
