---
type: llm
weight: 3
---

PASS if the analysis locates the cause in `Classes/Service/MessageSender.php` at the `$GLOBALS['TSFE']` lines (page id and `fe_user`), explains that the TypoScriptFrontendController / `$GLOBALS['TSFE']` no longer exists in TYPO3 14 (Breaking-107831 or Deprecation-105230), and proposes reading the page id and user from the request (`frontend.page.information` or `routing`, and `frontend.user` or the Context `frontend.user` aspect) passed in from the controller.
FAIL if the cause is not tied to TSFE, or the proposed fix still uses `$GLOBALS['TSFE']`.
