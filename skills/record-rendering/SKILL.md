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
