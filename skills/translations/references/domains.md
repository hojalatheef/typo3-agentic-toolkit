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
- `messages.xlf` present: `locallang.xlf` is never used for `messages`. No merging.
- Override the mapping with the PSR-14 event
  `TYPO3\CMS\Core\Localization\Event\BeforeLabelResourceResolvedEvent` (`$event->packageKey` read-only,
  `$event->domains` array domain => file, writable).
- CLI (EXT:lowlevel): `bin/typo3 language:domain:list [--extension=<key>]`, `bin/typo3 language:domain:search`.

Backend module labels (Feature-93334): one file per module with ids `title`, `short_description`,
`description`, referenced as `'labels' => 'my_extension.modules.my_module'` (several modules, file
`Resources/Private/Language/Modules/my_module.xlf`) or `'labels' => 'my_extension.module'` (single module,
file `module.xlf`).

## Fluid (Feature-107759, 14.0)

```html
<f:translate key="form.legend" domain="my_extension" />
<f:translate key="form.legend" domain="my_extension.messages" />
<f:translate key="my_extension.messages:form.legend" />
<f:translate key="LLL:my_extension.messages:form.legend" />
<f:translate key="users" domain="backend.messages" arguments="{0: count}" default="No users found" />
<f:translate key="label.title" domain="{myDomain}" />
```

`domain` wins over `extensionName`. Without either, the ViewHelper uses the Extbase extension name, then a
