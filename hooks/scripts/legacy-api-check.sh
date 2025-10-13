#!/usr/bin/env bash
# legacy-api-check.sh — PostToolUse hook for Edit and Write.
#
# Greps the file just written for APIs that TYPO3 v13/v14 deprecated or
# removed and hands Claude a hint per hit, citing the core changelog entry
# (read it with bin/t3-changelog <id>). Silent when nothing matches.
# Reads the hook payload on stdin.

set -euo pipefail

payload="$(cat)"
file="$(jq -r '.tool_input.file_path // empty' <<<"$payload" 2>/dev/null || true)"
[[ -n "$file" && -f "$file" ]] || exit 0
case "$file" in *.php|*.yaml|*.yml|*.typoscript|*.tsconfig|*.html) ;; *) exit 0 ;; esac

hints=()
hit() { command grep -Eq -- "$1" "$file"; }

[[ "$(basename "$file")" == ext_tables.php ]] \
  && hints+=("ext_tables.php is deprecated in 14.3 (Deprecation-109438): move modules to Configuration/Backend/Modules.php, routes to Configuration/Backend/Routes.php, the rest to TCA/Overrides.")
hit "\\\$GLOBALS\\[['\"]TSFE['\"]\\]|TypoScriptFrontendController" \
  && hints+=("\$GLOBALS['TSFE'] / TypoScriptFrontendController: deprecated in 13.4 (Deprecation-105230), removed in 14.0 (Breaking-107831). Read request attributes instead (frontend.page.information, frontend.typoscript, language).")
hit 'ExtensionUtility::registerModule|ExtensionManagementUtility::addModule' \
  && hints+=("registerModule()/addModule() were removed in 12.0 (Breaking-96733). Register modules in Configuration/Backend/Modules.php (Feature-96733).")
hit "list_type" \
  && hints+=("list_type plugins: deprecated in 13.4 (Deprecation-105076), tt_content.list_type removed in 14.0 (Breaking-105377, Important-105538). Register plugins as their own CType.")
hit 'Extbase\\Object\\ObjectManager|ObjectManagerInterface|->get\(ObjectManager' \
  && hints+=("Extbase ObjectManager: deprecated in 11.4 (Deprecation-94619), removed in 12.0 (Breaking-96107). Use constructor injection via Configuration/Services.yaml.")
hit 'addPItoST43' \
  && hints+=("ExtensionManagementUtility::addPItoST43(): deprecated (Deprecation-102821), removed in 14.0 (Breaking-105377).")
hit 'GeneralUtility::_(GP|GET|POST|GPmerged)\(' \
  && hints+=("GeneralUtility::_GP()/_GET()/_POST(): deprecated in 12.3 (Deprecation-100053), removed in 13.0 (Breaking-100963). Read the PSR-7 request (getQueryParams/getParsedBody).")
hit 'GeneralUtility::makeInstance\([A-Za-z\\]*(Repository|Service|ConnectionPool|EventDispatcher|Context)::class' \
  && hints+=("makeInstance() of a DI service: prefer constructor injection (the class must be public/autowired in Configuration/Services.yaml); makeInstance is for prototypes and static contexts.")

