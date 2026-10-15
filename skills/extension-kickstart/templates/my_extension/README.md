# My Extension

One sentence about what the extension does.

## Requirements

- TYPO3 14.3
- PHP 8.2 or newer

## Installation

```bash
composer require my-vendor/my-extension
vendor/bin/typo3 extension:setup
```

Then add the site set "My Extension" to your site: in the Sites module, or with
`dependencies: [my-vendor/my-extension]` in `config/sites/<site>/config.yaml`.

## Configuration

Site settings (category "My Extension"):

| Setting | Type | Default | Purpose |
| --- | --- | --- | --- |
| `myExtension.itemsPerPage` | int | `10` | Number of items shown on one list page |

## License

GPL-2.0-or-later
