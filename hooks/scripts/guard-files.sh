#!/usr/bin/env bash
# guard-files.sh — PreToolUse hook for Edit and Write.
#
#   ask   edits below vendor/, var/, public/typo3/, public/_assets/ (installed
#         or generated code: the next composer install or cache flush wipes
#         them). With the strict_guard option set to true this is a deny.
#   ask   config/system/settings.php (written by the Install Tool; prefer
#         config/system/additional.php or site settings)
# Everything else passes through. Reads the hook payload on stdin.

set -euo pipefail

payload="$(cat)"
file="$(jq -r '.tool_input.file_path // empty' <<<"$payload" 2>/dev/null || true)"
cwd="$(jq -r '.cwd // empty' <<<"$payload" 2>/dev/null || true)"
[[ -z "$file" ]] && exit 0
rel="${file#"${cwd%/}"/}"
strict="${CLAUDE_PLUGIN_OPTION_STRICT_GUARD:-${CLAUDE_PLUGIN_OPTION_strict_guard:-false}}"

decide() {
  jq -n --arg d "$1" --arg r "typo3-agentic-toolkit: $2" '{
    hookSpecificOutput: {hookEventName: "PreToolUse", permissionDecision: $d, permissionDecisionReason: $r}
  }'
  exit 0
}

