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

kind=""
if [[ -f ext_emconf.php ]] || jq -e '.type == "typo3-cms-extension"' composer.json >/dev/null 2>&1; then
  kind="extension"
elif [[ -d vendor/typo3/cms-core ]] || jq -e '.require["typo3/cms-core"]' composer.json >/dev/null 2>&1; then
  kind="composer"
elif [[ -d typo3_src || -d typo3/sysext || -d public/typo3/sysext ]]; then
  kind="classic"
fi
[[ -z "$kind" && -z "$version" ]] && exit 0
if [[ -z "$version" ]]; then
  declared="$(jq -r '.require["typo3/cms-core"] // empty' composer.json 2>/dev/null || true)"
  [[ -n "$declared" ]] && version="$declared (declared)"
fi

php=""
ddev=""
if [[ -f .ddev/config.yaml ]]; then
  ddev="ddev"
  php="$(sed -n 's/^php_version:[[:space:]]*"\{0,1\}\([0-9.]*\)"\{0,1\}.*/\1/p' .ddev/config.yaml | head -1)"
fi
[[ -z "$php" ]] && php="$(jq -r '.config.platform.php // .require.php // empty' composer.json 2>/dev/null || true)"

# Own sets only: skip installed packages and the core's system extensions.
sets="$(find . \( -path ./vendor -o -path ./node_modules -o -path '*/sysext' -o -path ./typo3_src \) -prune -o \
  -path '*/Configuration/Sets/*/config.yaml' -print 2>/dev/null | wc -l | tr -d ' ' || true)"
sites=""
[[ -d config/sites ]] && sites="$(find config/sites -maxdepth 2 -name config.yaml | wc -l | tr -d ' ')"

summary="TYPO3 ${version:-unknown version} · ${kind:-unknown mode}${ddev:+ · $ddev}${php:+ · PHP $php} · site sets: $sets${sites:+ · sites: $sites}"
hint="Use bin/t3 for the console (DDEV-aware) and bin/t3-changelog <id|keyword> to read core changelog entries before citing them."

