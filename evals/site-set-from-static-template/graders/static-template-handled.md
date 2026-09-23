---
type: llm
weight: 1
---

PASS if the reply says what happens to `addStaticFile()` in `sys_template.php`: removed (sets only), or kept on purpose for sites that still use sys_template records, with the reason given. Mentioning how to add the set to a site (`dependencies:` in `config/sites/<site>/config.yaml` or the Sites module) also counts toward PASS.
FAIL if the reply does not address the static template registration at all.
