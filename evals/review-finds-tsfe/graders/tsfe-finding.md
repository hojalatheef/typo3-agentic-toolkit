---
type: llm
weight: 3
---

PASS if the review flags `$GLOBALS['TSFE']` in `Classes/Service/MessageSender.php` (both `->id` and `->fe_user`) as broken on TYPO3 14, citing Breaking-107831 (TypoScriptFrontendController removed in 14.0) or Deprecation-105230 (13.4), and proposes reading the request instead: the page id from the `frontend.page.information` request attribute (or `routing`), the user from the `frontend.user` attribute or the `Context` aspect `frontend.user`, passed in from the controller's `$this->request`.
FAIL if the TSFE usage is not reported, or the fix still relies on `$GLOBALS['TSFE']` or `$GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.controller')`.
