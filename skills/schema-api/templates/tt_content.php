<?php

// EXT:my_extension/Configuration/TCA/Overrides/tt_content.php
// New content type with type-specific label config (Feature-108581, 14.2).
// System tabs (General, Language, Access, Notes) are added automatically
// (Feature-104814), so showitem lists only the element's own fields.

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

ExtensionManagementUtility::addRecordType(
    [
        'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tt_content.my_teaser',
        'description' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tt_content.my_teaser.description',
        'value' => 'my_teaser',
        'icon' => 'content-text',
        'group' => 'default',
    ],
    '
        --palette--;;headers,
        bodytext,
        image,
    ',
    [
        'label' => 'header',
        'label_alt' => 'bodytext',
    ]
);
