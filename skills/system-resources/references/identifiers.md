# Resource identifiers and URL generation

## Identifier syntax (Feature-107537, 14.0)

| Type | Identifier | Notes |
| --- | --- | --- |
| Package resource | `PKG:my-vendor/my-extension:Resources/Public/Icons/Extension.svg` | Composer name + path. Recommended for new code. |
| Package resource (legacy) | `EXT:my_extension/Resources/Public/Icons/Extension.svg` | Not deprecated yet. |
| App resource | `PKG:typo3/app:public/typo3temp/assets/style.css` | Use the real public dir name (`web`, `htdocs`, ...). |
