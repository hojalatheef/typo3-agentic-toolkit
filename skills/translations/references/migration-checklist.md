# Migration checklist: LLL:EXT paths to translation domains

Only for extensions that require `typo3/cms-core: ^14.0` (ICU and `TranslatorInterface`: `^14.2`). `LLL:EXT:`
references are not deprecated (Feature-93334), so this migration is about readability, not about removal.

## Prepare

1. List current files and their domains:
   `bin/typo3 language:domain:list --extension=my_extension`.
