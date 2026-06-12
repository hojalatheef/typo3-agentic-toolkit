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
```
