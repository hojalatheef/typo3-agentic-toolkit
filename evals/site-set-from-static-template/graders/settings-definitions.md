---
type: llm
weight: 2
---

PASS if the two former constants (`recipient`, `maxLength`) became settings in a `settings.definitions.yaml` next to the set's `config.yaml`, each with a `type` (string/int or email-like string, int) and a `default`, and the setup TypoScript reads them as `{$plugin.tx_contact.settings.…}` or the new setting names consistently.
FAIL if the constants were dropped, or kept only in `constants.typoscript` inside the set with no settings definitions.
