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
