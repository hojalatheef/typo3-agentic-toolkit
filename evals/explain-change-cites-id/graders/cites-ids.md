---
type: llm
weight: 3
---

PASS if the reply cites the core changelog entries by id: Deprecation-105076 (plugin content element and plugin sub types deprecated in 13.4) and at least one of Breaking-105377 (deprecated functionality removed in 14.0, including `tt_content.list_type`) or Important-105538 (list_type and sub types, 14.0).
FAIL if it cites no changelog id, or cites ids that do not exist (any `Feature-`/`Breaking-`/`Deprecation-` number other than these that it presents as the source for the list_type change, unless it is a real related entry).
