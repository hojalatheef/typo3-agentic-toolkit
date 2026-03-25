# Translation domains, ICU and XLIFF 2.0

## Domain syntax and resolution (Feature-93334, 14.0)

Format: `package[.subdomain...].resource:identifier`. The package is the extension key.

```php
$languageService->sL('backend.toolbar:save');
// same as
$languageService->sL('LLL:EXT:backend/Resources/Private/Language/locallang_toolbar.xlf:save');
```
