# Page rendering in a v14 site package

All behaviour below is from the cited changelog entries. Paths follow the
skeleton in [../templates/my_sitepackage/](../templates/my_sitepackage/).

## PAGEVIEW

Source: Feature-103504 (13.1), Feature-109167 (14.2), Feature-104974 (14.2).

```typoscript
page = PAGE
page.10 = PAGEVIEW
page.10 {
  paths.10 = EXT:my_sitepackage/Resources/Private/PageView/
  variables {
    brandName = TEXT
    brandName.value = {$sitepackage.brandName}
  }
  dataProcessing.10 = menu
  dataProcessing.10.as = mainMenu
}
```

- The backend layout name selects the template: layout `default` renders
  `Pages/Default.fluid.html` below the path (lowercase and `.html` variants
  are tried too). `Layouts/` and `Partials/` below the same path are wired
  automatically.
- Default variables: `settings`, `site`, `language`, `page`. They cannot be
  overridden (exception). `{page.pageLayout.identifier}` gives the layout.
