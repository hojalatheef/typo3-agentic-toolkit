# Migration checklist: LLL:EXT paths to translation domains

Only for extensions that require `typo3/cms-core: ^14.0` (ICU and `TranslatorInterface`: `^14.2`). `LLL:EXT:`
references are not deprecated (Feature-93334), so this migration is about readability, not about removal.

## Prepare

1. List current files and their domains:
   `bin/typo3 language:domain:list --extension=my_extension`.
2. Find collisions before renaming anything:
   - `locallang.xlf` next to `messages.xlf` - merge into one file.
   - `locallang_x.xlf` next to `x.xlf` - `locallang_x.xlf` is ignored; merge.
3. Decide on renames. Optional but clearer: `locallang.xlf` -> `messages.xlf`, `locallang_db.xlf` -> `db.xlf`.
   Rename the translated files too (`de.locallang.xlf` -> `de.messages.xlf`). The domain stays the same
   (`my_extension.messages`, `my_extension.db`), so references written in domain syntax survive the rename.
4. Grep all references:

```bash
grep -rn "LLL:EXT:my_extension/" Classes Configuration Resources ext_*.php
```

## Replace references

| Location | Before | After |
| --- | --- | --- |
| Fluid | `<f:translate key="LLL:EXT:my_extension/Resources/Private/Language/locallang_form.xlf:legend" />` | `<f:translate key="my_extension.form:legend" />` |
| Fluid | `<f:translate key="legend" extensionName="MyExtension" />` | `<f:translate key="legend" domain="my_extension.form" />` |
| TCA | `'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_my.title'` | `'label' => 'my_extension.db:tx_my.title'` |
| PHP | `$languageService->sL('LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:x')` | `$translator->translate('x', 'my_extension.messages')` |
| Modules.php | `'labels' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_mod.xlf'` | `'labels' => 'my_extension.module'` |
| Site set YAML | `label: 'LLL:EXT:my_extension/Configuration/Sets/Main/labels.xlf:x'` | `label: 'my_extension.sets.main:x'` |

Notes:

- TCA and other places that call `LanguageService::sL()` resolve domain references because the mapping is
  integrated into `LocalizationFactory` (Feature-93334 names TypoScript, Fluid, TCA and PHP as affected). Verify
  each location in the backend after migration.
- Module label files: rename ids `mlang_tabs_tab` -> `title`, `mlang_labels_tablabel` -> `short_description`,
  `mlang_labels_tabdescr` -> `description`.
- Core examples already use bare domain references in TCA, e.g. `'title' => 'my_extension.messages:my_title'`
  in the `ctrl` section (Feature-107289).

## Modernise label content

- Merge singular/plural label pairs into ICU messages and pass named arguments
  (`arguments="{count: n}"`, `['count' => $n]`).
