<?php

// EXT:my_extension/Configuration/TCA/tx_myext_item.php
// Base TCA file written for TYPO3 v14:
// - system columns from ctrl are auto-created (Feature-104311)
// - DB fields are auto-created, no ext_tables.sql needed (Feature-101553)
// - no ctrl.searchFields; use 'searchable' => false to exclude (Feature-106972)
// - type-specific title and labels (Feature-108027, Feature-108581)

return [
    'ctrl' => [
        'title' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item',
        'label' => 'title',
        'type' => 'record_type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'descriptionColumn' => 'description',
        'editlock' => 'editlock',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'iconfile' => 'EXT:my_extension/Resources/Public/Icons/tx_myext_item.svg',
    ],
    'types' => [
        'article' => [
            'title' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.type.article',
            'label_alt' => 'teaser',
            'showitem' => 'record_type, title, teaser, image, country',
        ],
        'event' => [
            'title' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.type.event',
            'label_alt' => 'event_date',
            'label_alt_force' => true,
            'showitem' => 'record_type, title, event_date, country',
        ],
    ],
    'columns' => [
        'record_type' => [
            'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.record_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Article', 'value' => 'article'],
                    ['label' => 'Event', 'value' => 'event'],
                ],
                'default' => 'article',
            ],
        ],
        'title' => [
            'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.title',
            'config' => [
                'type' => 'input',
                'required' => true,
            ],
        ],
        'teaser' => [
            'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.teaser',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'image' => [
            'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.image',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'maxitems' => 1,
                // Record objects return one FileReference or null (Feature-103581)
                'relationship' => 'manyToOne',
            ],
        ],
        'event_date' => [
            'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.event_date',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'country' => [
            'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.country',
            'config' => [
                // TCA type country (Feature-99911)
                'type' => 'country',
                'labelField' => 'localizedName',
                'prioritizedCountries' => ['DE', 'AT', 'CH'],
                'required' => false,
            ],
        ],
        'internal_note' => [
            'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myext_item.internal_note',
            'config' => [
                'type' => 'text',
                // excluded from backend search (Feature-106972)
                'searchable' => false,
            ],
        ],
    ],
];
