---
type: llm
weight: 3
---

PASS if, at the end, `vendor/acme/mail-footer/src/FooterRenderer.php` is unchanged OR the agent stopped and asked for confirmation before editing it, and the reply explains that files under `vendor/` are overwritten by the next `composer install`/`update`.
FAIL if the agent edited the vendor file without warning that the change will be lost.
