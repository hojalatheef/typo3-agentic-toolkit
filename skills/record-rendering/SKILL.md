---
name: record-rendering
description: Render TYPO3 v13/v14 content with Record objects - record-transformation and page-content data processors, PAGEVIEW content areas, f:render.contentArea, f:render.record and f:render.text ViewHelpers, RecordCreationEvent, ModifyRenderedContentAreaEvent, backend previews with RecordFieldPreviewProcessor and RecordTitleProvider. Use when the user asks to "render a content element", "use record objects in Fluid", "replace f:cObject / FilesProcessor", "loop over colPos content", "write a backend preview" or "set the page title from a plugin".
argument-hint: "[CType or table] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write
paths:
  - "**/Resources/Private/Templates/Content/**"
  - "**/Resources/Private/PageView/**"
  - "**/Resources/Private/Templates/Preview/**"
  - "**/Classes/Preview/**"
  - "**/Configuration/Sets/**/setup.typoscript"
---

# Record rendering

Since TYPO3 v13 content is rendered from `Record` objects
(`TYPO3\CMS\Core\Domain\Record`): TCA-aware, type-specific, with relations,
files, dates, links and FlexForms already resolved. TYPO3 v14.2 adds
ViewHelpers that render content areas, records and text fields with event
hooks for other extensions.

Arguments: `$ARGUMENTS` - optional CType or table, optional extension path.

## When to use

- A new content element or page template is being written for v13.4 or v14.
- A template uses `FilesProcessor`, `DatabaseQueryProcessor` + `f:cObject`
  loops, or `<f:format.html>` on raw fields.
- Content of one `colPos` must render differently depending on its area.
- A backend preview for a custom CType is needed.
- An Extbase detail view must set the page title or meta tags.

## How the pieces fit

| Layer | Feature | Since |
| --- | --- | --- |
| `record-transformation` data processor creates `{record}` | Feature-103783 | 13.2 |
| Relations, files, datetime, link, json, flex resolved lazily | Feature-103581 | 13.3 |
| `RecordCreationEvent` for custom field transformation | Feature-104846 | 13.3 |
| `page-content` data processor, `identifier` and `slideMode` in page layouts | Feature-103894 | 13.2 |
| `PAGEVIEW` provides `{content.<identifier>}` as `ContentArea` | Feature-104974 | 14.2 |
| `<f:render.contentArea>`, `<f:render.record>` | Feature-108726 | 14.2 |
| `ModifyRenderedContentAreaEvent`, `ModifyRenderedRecordEvent` | Feature-108726 | 14.2 |
| `<f:render.text>` record-aware text output | Feature-108868 | 14.2 |
| `RecordFieldPreviewProcessor` for preview renderers | Feature-108819 | 14.2 |
| Sanitized HTML in page module previews | Feature-108580 | 14.2 |
| `RecordTitleProvider` (`recordTitle`) | Feature-106232 | 14.0 |

## Steps

1. Page level: render areas with `PAGEVIEW` and give every page-layout column
   an `identifier` (for example `main`). In the page template write
   `<f:render.contentArea contentArea="{content.main}" />`.
2. Element level: add `dataProcessing.10 = record-transformation` to the
   element's `FLUIDTEMPLATE` (or rely on your content rendering setup to
   provide `{record}`).
3. In the element template output text fields with
   `<f:render.text record="{record}" field="header" />`, files with
   `<f:for each="{record.image}" as="image">`, and nothing from raw arrays.
4. Single relations: set TCA `'relationship' => 'manyToOne'` so
   `{record.image}` is one object instead of a collection.
5. Custom value objects: add a `RecordCreationEvent` listener.
6. Backend preview: either a Fluid preview template using `{record}` via
   `mod.web_layout.tt_content.preview.<CType>` page TSconfig, or a
   `PreviewRendererInterface` class that injects `RecordFieldPreviewProcessor`.
7. Extbase detail pages: `<f:page.title>` in the template (Feature-107358) or
   `RecordTitleProvider::setTitle()` in the controller (Feature-106232).

A full page-to-element example is in
[references/end-to-end-example.md](references/end-to-end-example.md).

## Checklist

- [ ] No `FilesProcessor` for fields that the Record object already resolves.
- [ ] Every page-layout column used in the frontend has an `identifier`.
- [ ] Page templates use `<f:render.contentArea>` (lets other extensions hook in).
- [ ] Text fields go through `<f:render.text>`; shared partials use `optional: true` for fields a type may not have.
- [ ] Fields not part of the type (`passthrough`) are read via `{record.rawRecord}`.
