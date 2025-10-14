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

