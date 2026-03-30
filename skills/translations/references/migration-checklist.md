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
