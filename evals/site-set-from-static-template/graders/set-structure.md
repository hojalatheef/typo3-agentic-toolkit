---
type: llm
weight: 3
---

PASS if the agent created `Configuration/Sets/<Folder>/config.yaml` with a `name` in `vendor/name` form (for example `acme/contact`) and a `label`, and moved or copied the TypoScript setup into that set folder (`setup.typoscript` next to `config.yaml`), as described in Feature-103437 (site sets, 13.1).
FAIL if no `Configuration/Sets/*/config.yaml` exists at the end, or the set has no `name`.
