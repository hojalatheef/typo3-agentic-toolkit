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
