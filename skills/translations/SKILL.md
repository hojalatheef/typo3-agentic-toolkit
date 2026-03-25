---
name: translations
description: >-
  Write and modernise TYPO3 v14 labels - translation domains (my_extension.messages:key) instead of LLL:EXT paths, the f:translate domain attribute, XLIFF 2.0 files, ICU MessageFormat plurals and selects, the generic TranslatorInterface, labels as JavaScript modules (~labels/...), localized enum labels in site settings and the Symfony translation loader configuration under TYPO3_CONF_VARS[LANG]. Use when creating or editing .xlf files, f:translate calls, LanguageService/LocalizationUtility code, or when the user asks to "pluralize a label", "switch to translation domains", "use XLIFF 2" or "migrate LLL paths".
argument-hint: "[extension path or .xlf file] [migrate|plural|xliff2|js]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(*/bin/typo3 language:domain:*), Bash(ddev exec*language:domain:*)
paths:
  - "**/Resources/Private/Language/**/*.xlf"
  - "**/Configuration/Sets/**/labels.xlf"
  - "**/Configuration/Sets/**/settings.definitions.yaml"
  - "**/Resources/Private/**/*.html"
---

# Translations in TYPO3 v14

Arguments: `$ARGUMENTS`.

TYPO3 14 reads label files through the Symfony Translation component (Feature-107436, 14.0) and adds a
short reference syntax, the *translation domain*: `<extension_key>.<resource>:<identifier>`
(Feature-93334, 14.0). `LLL:EXT:` paths keep working "for a long time" without deprecation, so adopt
domains incrementally.

## When to use

- New labels in an extension targeting 14.x: use domains in TCA, Fluid, PHP and `Modules.php`.
- `<f:translate>` calls with long `LLL:EXT:` keys or `extensionName`: switch to `domain` / inline domain
  (Feature-107759, 14.0).
