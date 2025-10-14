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

abs="$(cd "$(dirname "$file")" && pwd)/$(basename "$file")"
ddev_root="$(d="$(dirname "$abs")"; while [[ "$d" != / && ! -d "$d/.ddev" ]]; do d="$(dirname "$d")"; done; [[ -d "$d/.ddev" ]] && echo "$d" || true)"

lint_ddev()   { [[ -n "$ddev_root" ]] && command -v ddev >/dev/null && (cd "$ddev_root" && ddev exec php -l "${abs#"$ddev_root"/}" 2>&1); }
lint_host()   { php -r 'exit(0);' >/dev/null 2>&1 && php -l "$abs" 2>&1; }
lint_docker() { command -v docker >/dev/null && docker info >/dev/null 2>&1 \
                && docker run --rm -v "$(dirname "$abs")":/w -w /w php:8.4-cli php -l "$(basename "$abs")" 2>&1; }

out=""
case "$runner" in
  ddev)   out="$(lint_ddev)" || true ;;
  host)   out="$(lint_host)" || true ;;
  docker) out="$(lint_docker)" || true ;;
  *)
    for try in lint_ddev lint_host lint_docker; do
      out="$($try)" || true
      [[ "$out" == *"No syntax errors"* || "$out" == *"Parse error"* || "$out" == *"Fatal error"* ]] && break
    done ;;
esac

[[ "$out" == *"Parse error"* || "$out" == *"Fatal error"* ]] || exit 0
msg="php -l failed for $file: $(grep -E 'error' <<<"$out" | head -3 | tr '\n' ' ')"
jq -n --arg m "$msg" '{
  decision: "block",
  reason: $m,
  hookSpecificOutput: {hookEventName: "PostToolUse", additionalContext: ($m + " Fix the syntax before continuing.")}
}'
exit 0
