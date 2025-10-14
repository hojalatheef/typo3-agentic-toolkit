#!/usr/bin/env bash
# precompact-notes.sh — PreCompact hook.
#
# Before the context is compacted, appends the facts worth keeping to
# ${CLAUDE_PLUGIN_DATA}/notes/<session>.md: time, trigger, working
# directory, TYPO3 version, git branch and the files changed so far. The
# notes survive the compaction; read them back when the summary is thin.
# Reads the hook payload on stdin; prints nothing.

set -euo pipefail

payload="$(cat)"
session="$(jq -r '.session_id // "unknown"' <<<"$payload" 2>/dev/null || echo unknown)"
trigger="$(jq -r '.trigger // "auto"' <<<"$payload" 2>/dev/null || echo auto)"
cwd="$(jq -r '.cwd // empty' <<<"$payload" 2>/dev/null || true)"
[[ -n "$cwd" && -d "$cwd" ]] && cd "$cwd"

dir="${CLAUDE_PLUGIN_DATA:-${XDG_DATA_HOME:-$HOME/.local/share}/typo3-agentic-toolkit}/notes"
mkdir -p "$dir"
notes="$dir/${session//[^A-Za-z0-9_-]/_}.md"

version="$(jq -r '[.packages[]? | select(.name == "typo3/cms-core") | .version][0] // empty' composer.lock 2>/dev/null || true)"
branch="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || true)"
changed="$(git status --porcelain 2>/dev/null | head -30 | sed 's/^/    /' || true)"

{
  [[ -s "$notes" ]] || printf '# Session notes %s\n' "$session"
  printf '\n## Compaction %s (%s)\n\n' "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$trigger"
  printf -- '- cwd: %s\n' "${cwd:-$PWD}"
  [[ -n "$version" ]] && printf -- '- TYPO3: %s\n' "$version"
  [[ -n "$branch" ]] && printf -- '- branch: %s\n' "$branch"
  if [[ -n "$changed" ]]; then printf -- '- changed files:\n\n%s\n' "$changed"; fi
} >>"$notes"
exit 0
