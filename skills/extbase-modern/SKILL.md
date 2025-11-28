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
