---
name: system-resources
description: >-
  Reference and publish assets in TYPO3 v14 with the System Resource API - PKG:vendor/package:path, EXT:, FAL:, URI: and typo3/app identifiers, SystemResourceFactory + SystemResourcePublisherInterface in PHP, f:resource / f:uri.resource in Fluid, Configuration/Resources.php and config/system/resources.php, asset:publish with link/mirror publishing, CDN publishers, static asset routes and cache busting. Use when generating asset URLs in PHP, Fluid or TypoScript, when replacing PathUtility::getPublicResourceWebPath, createVersionNumberedFilename or FilePathSanitizer, when assets live outside Resources/Public, or when the user asks about "_assets", "asset publishing", "favicon route" or "read-only deployments".
argument-hint: "[path or identifier] [php|fluid|typoscript|publish]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(*/bin/typo3 asset:publish*)
paths:
  - "**/Configuration/Resources.php"
  - "**/config/system/resources.php"
  - "**/Configuration/TypoScript/**/*.typoscript"
  - "**/Resources/Private/Layouts/**/*.html"
---

# System resources and public assets (TYPO3 v14)

Arguments: `$ARGUMENTS`.
