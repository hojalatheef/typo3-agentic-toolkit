---
type: llm
weight: 3
---

PASS if `composer.json` at the repository root has `"type": "typo3-cms-extension"`, `extra.typo3/cms.extension-key` set to `event_calendar` (or another valid key the agent explains), an extension version in `extra.typo3/cms.version` or the top-level `version`, `extra.typo3/cms.Package.providesPackages` (an empty object is fine), and a `description` that starts with "Event Calendar - " (Breaking-108304, Feature-108345).
FAIL if any of these fields is missing.
