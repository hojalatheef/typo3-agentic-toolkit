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
`domain:` prefix in the key, then the extension key of an `LLL:EXT:` path.

## PHP: TranslatorInterface and LanguageService (Feature-89334, 14.2)

`TYPO3\CMS\Core\Localization\TranslatorInterface` is implemented by `LanguageService`.

| Method | Purpose |
| --- | --- |
| `translate(string $id, string $domain, array $arguments = [], ?string $default = null, Locale\|string\|null $locale = null)` | Identifier + domain (or `EXT:` file, discouraged). |
| `label(string $reference, array $arguments = [], ?string $default = null, Locale\|string\|null $locale = null)` | Full reference: `my_ext.messages:key`, `EXT:...xlf:key` or `LLL:EXT:...xlf:key` (`LLL:` optional). |

Both return `string|\Stringable|null` (signatures checked against the core source; `null` means "not found").
`LanguageService::translate()` gained the `$default` and `$locale` parameters.

```php
public function __construct(private readonly TranslatorInterface $translator) {}

$this->translator->translate('button.save', 'my_extension.messages');
$this->translator->translate('record.count', 'my_extension.messages', [5]);
$this->translator->translate('missing.key', 'my_extension.messages', [], 'Fallback text');
$this->translator->label('my_extension.messages:record.count', [5], 'Fallback text');
```

## ICU MessageFormat (Feature-104546, 14.2)

Associative arguments plus ICU patterns in the message trigger PHP intl's `MessageFormatter`. Indexed arguments
keep `sprintf`.

```xml
<trans-unit id="file_count">
    <source>{count, plural, one {# file} other {# files}}</source>
</trans-unit>
<trans-unit id="item_count">
    <source>{count, plural, =0 {no items} one {# item} other {# items}}</source>
</trans-unit>
<trans-unit id="profile_update">
    <source>{gender, select, male {He} female {She} other {They}} updated the profile.</source>
</trans-unit>
```

```php
$languageService->translate('file_count', 'my_extension.messages', ['count' => 5]); // "5 files"
LocalizationUtility::translate('file_count', 'MyExtension', ['count' => 1]);       // "1 file"
```

```html
<f:translate key="file_count" arguments="{count: numberOfFiles}" />
<f:translate key="downloaded_times" arguments="{0: downloadCount}" />
```

Also available: `{count, number}`, `{price, number, currency}`. `#` is the number inside plural branches.
Requires the PHP `intl` extension.

## JavaScript label modules (Feature-108941, 14.2)

```javascript
import labels from '~labels/core.bookmarks';

labels.get('groupType.global');
labels.get('file.deleted', { filename: 'my-file.txt' });
labels.get('file.deleted', {
    filename: 'my-file.txt',
    bold: chunks => html`<strong>${chunks}</strong>`,
});
```

The `~labels/<domain>` prefix is resolved through the import map; URLs are version- and locale-specific so
browsers cache them long-term. Replaces pushing labels into `TYPO3.lang`.

## Site settings enum labels (Feature-106640, 14.2)

List-style enums derive keys `settings.<settingKey>.enum.<value>` from the set's `labels.xlf`:

```yaml
settings:
  my.enumSetting:
    type: string
    default: optionA
    enum:
      - optionA
      - optionB
```

```xml
<trans-unit id="settings.my.enumSetting.enum.optionA">
  <source>Option A</source>
</trans-unit>
```

Map-style enums use only the given value: an `LLL:` reference is resolved, a literal stays literal, a key-only
entry falls back to the value, `''` stays empty.

## XLIFF 2.0 and loader configuration (Feature-107710, Feature-107436, 14.0)

The loader detects XLIFF 1.2 or 2.x by namespace and `version`. In 2.0, `<unit>` contains `<segment>`,
languages are `srcLang` / `trgLang` on `<xliff>`, and approval is the segment `state`: `initial` and
`translated` count as not approved, `reviewed` and `final` as approved when
