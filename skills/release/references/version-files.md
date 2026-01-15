# Version files of a TYPO3 extension

## ext_emconf.php

```php
$EM_CONF[$_EXTKEY] = [
    'title' => 'My extension',
    'version' => '3.2.0',
    'state' => 'stable',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
        ],
    ],
];
```

- TER rejects an upload whose `version` differs from the tag.
- The `typo3` range must match the `typo3/cms-core` constraint in `composer.json` (`^13.4 || ^14.3` corresponds to `13.4.0-14.3.99` or a wider upper bound you have tested).
- `state` is `stable` for releases, `beta` or `alpha` otherwise.

## composer.json

- Packagist takes the version from the git tag. Leave out the `version` key; if it is present, keep it in sync or remove it in a separate commit.
- `extra.typo3/cms.extension-key` must be set.
