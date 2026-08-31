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
- Labels that need plurals, gender or named placeholders: ICU MessageFormat (Feature-104546, 14.2).
- Services that translate: inject `TYPO3\CMS\Core\Localization\TranslatorInterface` (Feature-89334, 14.2).
- Backend JavaScript/web components needing labels: `import labels from '~labels/<domain>'`
  (Feature-108941, 14.2).
- Site set `enum` settings with translated option labels (Feature-106640, 14.2).
- New translation files in XLIFF 2.0 (Feature-107710, 14.0).
- A custom localization parser or `SYS.lang` / `locallangXMLOverride` configuration exists (Breaking-107436).

## Steps

1. Inventory: `bin/typo3 language:domain:list --extension=<ext_key>` (EXT:lowlevel) prints every domain of the
   extension and its file. `language:domain:search` finds label texts.
2. Map each file to its domain with the rules in [references/domains.md](references/domains.md)
   (`locallang.xlf` -> `<ext>.messages`, `locallang_db.xlf` -> `<ext>.db`, `Form/locallang_tabs.xlf` ->
   `<ext>.form.tabs`, set `labels.xlf` -> `<ext>.sets.<set>`).
3. Replace references per the [migration checklist](references/migration-checklist.md). Do one file type at a
   time (Fluid, TCA, PHP, YAML) and run the extension's tests between steps.
4. Where a label is concatenated from singular/plural variants, convert it to one ICU message and pass named
   arguments. Positional arguments keep `sprintf` behaviour.
5. In services, type-hint `TranslatorInterface` and call `translate($id, $domain, $arguments, $default)` or
   `label('<domain>:<id>')`. `label()` returns `null` when unresolved - handle it.
6. For dual 13.4 + 14 extensions keep `LLL:EXT:` references: domain syntax, ICU and `TranslatorInterface` do
   not exist in 13.4.
7. Copy [templates/messages.xlf](templates/messages.xlf) (XLIFF 1.2 with ICU) or
   [templates/messages.de.xliff2.xlf](templates/messages.de.xliff2.xlf) (XLIFF 2.0) as a starting point.

## Checklist

- [ ] No `locallang.xlf` and `messages.xlf` side by side in one directory (the latter wins, no merge).
- [ ] No two files that map to the same domain (`locallang_db.xlf` + `db.xlf`: `locallang_db.xlf` is ignored).
- [ ] Fluid: `key="<domain>:<id>"` or `key="<id>" domain="<domain>"`; no new `extensionName` usages.
- [ ] ICU messages get associative `arguments="{count: n}"`; sprintf messages get `{0: n}`.
- [ ] XLIFF 2.0 targets carry `state="final"` or `"reviewed"` when `requireApprovedLocalizations` is on.
- [ ] Backend module labels use `title`, `short_description`, `description` identifiers and a domain
      (`<ext>.module` or `<ext>.modules.<name>`).
- [ ] Settings `enum` lists use derived keys `settings.<key>.enum.<value>` in the set `labels.xlf`.
- [ ] `$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']` moved to `['LANG']['resourceOverrides']`.
- [ ] No `['SYS']['lang']['parser']`; custom formats are Symfony loaders in `['LANG']['loader']`.

## Pitfalls

- The ICU formatter is used only when the arguments array is associative *and* the message has ICU patterns.
  Passing `[5]` to `{count, plural, ...}` does not pluralise.
