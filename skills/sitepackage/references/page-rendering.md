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
- Since 14.2 the content of every backend-layout column with an `identifier`
  is available as `{content.<identifier>}` (`ContentArea`); `contentAs`
  renames the variable. On v13.4 use `dataProcessing.10 = page-content`
  (Feature-103894).
- If a template is missing, v14.2 lists every path that was tried, with the
  `PAGEVIEW` context (Feature-109167).

Migration from `FLUIDTEMPLATE`: drop the `templateName` stdWrap that read
`backend_layout`, the three `*RootPaths` and `variables` like `pageUid` or
`pageTitle` - use `{page.uid}` and `{page.title}`.

## Content areas

Source: Feature-104974, Feature-108726 (14.2).

```html
<main>
    <f:render.contentArea contentArea="{content.main}" />
</main>
```

`<f:render.contentArea>` renders each record through its TypoScript
definition and lets other extensions modify the output via
`ModifyRenderedContentAreaEvent`. Per-record wrapping uses `recordAs` plus
`<f:render.record>`. See the `record-rendering` skill for element templates.
