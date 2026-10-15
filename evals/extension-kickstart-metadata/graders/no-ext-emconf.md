---
type: llm
weight: 2
---

PASS if no `ext_emconf.php` was created, or the reply gives a concrete reason for one (the prompt rules out TER and classic mode, so a reason is unlikely to hold). Mentioning Deprecation-108345 or that composer.json now carries the metadata also counts.
FAIL if `ext_emconf.php` exists without a stated reason.
