#!/usr/bin/env bash
# php-lint.sh — PostToolUse hook (Edit|Write, runs async) for *.php files.
#
# Runs `php -l` on the written file with the runner the php_runner option
# names: auto (DDEV when .ddev/ exists and the project runs, then host php,
# then a php:8.4-cli container), ddev, host or docker. A syntax error goes
# back to Claude (decision block + additionalContext); a clean file and a
# missing PHP stay silent. Reads the hook payload on stdin.

set -euo pipefail

payload="$(cat)"
file="$(jq -r '.tool_input.file_path // empty' <<<"$payload" 2>/dev/null || true)"
cwd="$(jq -r '.cwd // empty' <<<"$payload" 2>/dev/null || true)"
[[ "$file" == *.php && -f "$file" ]] || exit 0
[[ -n "$cwd" && -d "$cwd" ]] && cd "$cwd"
runner="${CLAUDE_PLUGIN_OPTION_PHP_RUNNER:-${CLAUDE_PLUGIN_OPTION_php_runner:-auto}}"

