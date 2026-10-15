---
type: llm
weight: 2
---

PASS if a site set exists at `Configuration/Sets/*/config.yaml` with `name: acme/event-calendar` (or another `vendor/name` value), and the plugin is registered as its own content type: `ExtensionUtility::configurePlugin()` in `ext_localconf.php` without a `list_type` argument, plus `ExtensionUtility::registerPlugin()` in `Configuration/TCA/Overrides/tt_content.php` (Important-105538).
FAIL if there is no site set, if the plugin uses `list_type` or `ExtensionUtility::PLUGIN_TYPE_PLUGIN`, or if TypoScript is registered with `addStaticFile()` only.
