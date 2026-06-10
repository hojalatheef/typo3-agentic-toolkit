# End-to-end: page layout to content element with Record objects

A custom content element `my_teaser` with a header, rich-text body, one image
and a link, rendered on a TYPO3 v14.2+ page. Each step names the changelog
entry it relies on. For v13.4, replace the render ViewHelpers by the `f:for`
and `f:cObject` loop shown at the end.

## 1. Page layout with identifiers

Page TSconfig, for example in the site set's `page.tsconfig` (Feature-103894,
Feature-104974):

```typoscript
mod.web_layout.BackendLayouts {
  default {
    title = Default
    config {
      backend_layout {
        colCount = 2
        rowCount = 1
        rows {
          1 {
            columns {
              1 {
                name = Main Content Area
                colPos = 0
                identifier = main
              }
              2 {
                name = Sidebar
                colPos = 1
                identifier = sidebar
                slideMode = slide
              }
            }
          }
        }
      }
    }
  }
}
```

`slideMode` accepts `slide`, `collect` and `collectReverse`.

## 2. TypoScript: page and element

In the site set's `setup.typoscript`:

```typoscript
page = PAGE
page.10 = PAGEVIEW
page.10.paths.10 = EXT:my_sitepackage/Resources/Private/PageView/

tt_content.my_teaser = FLUIDTEMPLATE
tt_content.my_teaser {
  templateName = Teaser
  templateRootPaths.10 = EXT:my_sitepackage/Resources/Private/Templates/Content/
  dataProcessing.10 = record-transformation
}
```

- `PAGEVIEW` resolves `Pages/<LayoutName>` below the path and exposes the
  areas as `{content}` (Feature-103504, Feature-104974). Use `contentAs` to
  rename the variable.
- `record-transformation` turns the element's data into `{record}`
  (Feature-103783). Options: `variableName`, `table`, `as`.

## 3. Page template

`Resources/Private/PageView/Pages/Default.fluid.html`:

```html
<main>
    <f:render.contentArea contentArea="{content.main}" />
</main>
<aside>
    <f:render.contentArea contentArea="{content.sidebar}" recordAs="record">
        <div class="sidebar-item sidebar-item--{record.recordType}">
            <f:render.record record="{record}" />
        </div>
    </f:render.contentArea>
</aside>
```

`<f:render.contentArea>` and `<f:render.record>` (Feature-108726, 14.2) render
through the TypoScript definition of the record's table and type, and fire
`ModifyRenderedContentAreaEvent` / `ModifyRenderedRecordEvent` so other
extensions can wrap the output. `{content.main}` also carries `identifier`,
`colPos`, `name`, `allowedContentTypes`, `disallowedContentTypes`,
