# Translation domains, ICU and XLIFF 2.0

## Domain syntax and resolution (Feature-93334, 14.0)

Format: `package[.subdomain...].resource:identifier`. The package is the extension key.

```php
$languageService->sL('backend.toolbar:save');
// same as
$languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_toolbar.xlf:save');
```

Domain names are generated from the files below `Resources/Private/Language/`:

| File | Domain |
| --- | --- |
| `locallang.xlf` or `messages.xlf` | `<ext>.messages` |
| `locallang_toolbar.xlf` | `<ext>.toolbar` |
| `locallang_sudo_mode.xlf` | `<ext>.sudo_mode` |
| `Form/locallang_tabs.xlf` | `<ext>.form.tabs` |
| `Configuration/Sets/Felogin/labels.xlf` | `<ext>.sets.felogin` |
| `SudoMode.xlf` | `<ext>.sudo_mode` (UpperCamelCase to snake_case) |
| `de.locallang.xlf` | `<ext>.messages`, German translation |

Rules:

- The mapping is built once per package by scanning the directory and cached in `cache.l10n`.
- `locallang_db.xlf` and `db.xlf` together: `locallang_db.xlf` is ignored.
