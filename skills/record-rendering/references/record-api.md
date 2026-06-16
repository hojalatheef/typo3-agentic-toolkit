# Record API reference

Collected from Feature-103783, Feature-103581, Feature-104846, Feature-108726,
Feature-108868, Feature-108819, Feature-106232 and Breaking-107356. Use only
what is listed; the PHP API beyond Fluid usage was marked "details to be
finalized" in 13.2.

## Properties in Fluid

```html
{record.uid} {record.pid} {record.title}          <!-- any field of the type -->
{record.languageId}
{record.languageInfo.translationParent}
{record.languageInfo.translationSource}
{record.overlaidUid}
{record.mainType}      <!-- "tt_content" -->
{record.recordType}    <!-- "textpic" -->
{record.fullType}      <!-- "tt_content.textpic" -->
{record.systemProperties.deleted}
{record.systemProperties.disabled}
{record.systemProperties.lockedForEditing}
{record.systemProperties.createdAt}
{record.systemProperties.lastUpdatedAt}
{record.systemProperties.publishAt}
{record.systemProperties.publishUntil}
{record.systemProperties.userGroupRestriction}
{record.systemProperties.sorting}
{record.systemProperties.description}
{record.computedProperties.versionedUid}
{record.computedProperties.localizedUid}
{record.computedProperties.requestedOverlayLanguageId}
{record.computedProperties.translationSource}
{record.versionInfo.workspaceId}
{record.versionInfo.liveId}
{record.versionInfo.state.name}
{record.versionInfo.stageId}
{record.rawRecord}     <!-- untransformed row, e.g. for passthrough fields -->
```

Only fields of the current type are properties. Accessing a missing field via
`<f:render.text>` throws `RecordPropertyNotFoundException` unless
`optional: true` is set (Feature-108868).

## Field transformations

Feature-103581 (13.3), resolved lazily on first access:

| TCA type | Value in the Record |
| --- | --- |
| `category`, `group`, `inline`, `select` with `foreign_table`/`MM` | `LazyRecordCollection` of Records |
| `file` | `LazyFileReferenceCollection` of `FileReference` |
| `folder` | `LazyFolderCollection` of `Folder` |
| `datetime` | `DateTimeImmutable` |
| `flex` | resolved FlexForm, values expanded |
| `json` | decoded value |
| `link` | `TypolinkParameter` |
| `select` with static items | array of values |

TCA `relationship` (`oneToOne`, `manyToOne`) returns the single object or
`null` instead of a collection. `maxitems` does not change this.
`selectSingle` with `foreign_table` defaults to `manyToOne` (`manyToMany` with
`MM`).
