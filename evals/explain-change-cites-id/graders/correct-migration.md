---
type: llm
weight: 2
---

PASS if the reply says plugins are registered as their own content type (CType): `ExtensionUtility::configurePlugin()` without `list_type` as plugin type (CType is the default/only type in 14), `registerPlugin()` in TCA/Overrides/tt_content.php adding a CType, an upgrade wizard or migration for existing `list_type` records, and FlexForm/TypoScript references moving from `list_type`/`tt_content.list.20.*` to the CType.
FAIL if it tells the user to keep `list_type` in v14, or invents a new registration API.
