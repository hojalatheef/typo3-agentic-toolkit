# Content Blocks basics

Applies to `friendsoftypo3/content-blocks` 1.x (checked against 1.0.4 on
TYPO3 13.4). This is an extension, not core. Newer or older releases may
differ - **check the extension docs for your version** before copying keys.

## Folder layout

A host extension (usually the site package) holds the blocks:

```text
my_sitepackage/
└── ContentBlocks/
    ├── ContentElements/
    │   └── teaser/
    │       ├── config.yaml
    │       ├── assets/
    │       │   ├── icon.svg
    │       │   └── frontend.css        (optional)
    │       ├── language/
    │       │   └── labels.xlf
    │       └── templates/
    │           ├── frontend.html
    │           └── backend-preview.html  (optional)
    ├── PageTypes/
    └── RecordTypes/
```

Folder names `ContentElements`, `PageTypes`, `RecordTypes`, `assets`,
`language` and `templates` are conventions read by the extension. Content
