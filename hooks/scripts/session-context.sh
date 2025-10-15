#!/usr/bin/env bash
# session-context.sh — SessionStart and FileChanged (composer.lock) hook.
#
# Detects the TYPO3 setup in the working directory and hands Claude a short
# summary: core version, Composer or classic mode, extension or project, DDEV
# and its PHP version, and how many site sets exist. On SessionStart the
# summary goes in as additionalContext (and TYPO3_* variables into
# $CLAUDE_ENV_FILE when set, plus a watch on composer.lock); on FileChanged
# it is a systemMessage.
# Prints nothing outside a TYPO3 directory. Reads the hook payload on stdin.

set -euo pipefail

payload="$(cat)"
cwd="$(jq -r '.cwd // empty' <<<"$payload" 2>/dev/null || true)"
event="$(jq -r '.hook_event_name // "SessionStart"' <<<"$payload" 2>/dev/null || echo SessionStart)"
[[ -n "$cwd" && -d "$cwd" ]] && cd "$cwd"

version=""
if [[ -f composer.lock ]]; then
  version="$(jq -r '[.packages[]? | select(.name == "typo3/cms-core") | .version][0] // empty' composer.lock 2>/dev/null || true)"
fi
for f in vendor/typo3/cms-core/Classes/Information/Typo3Version.php \
         typo3_src/typo3/sysext/core/Classes/Information/Typo3Version.php \
         public/typo3/sysext/core/Classes/Information/Typo3Version.php \
         typo3/sysext/core/Classes/Information/Typo3Version.php; do
  [[ -z "$version" && -f "$f" ]] || continue
  version="$(sed -n "s/.*const VERSION = '\([^']*\)'.*/\1/p" "$f" | head -1)"
done
version="${version#v}"

