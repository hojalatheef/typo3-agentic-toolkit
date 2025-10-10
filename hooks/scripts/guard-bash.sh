#!/usr/bin/env bash
# guard-bash.sh — PreToolUse hook for Bash.
#
#   deny  git ... --no-verify            (skipping hooks hides failures)
#   deny  composer ... --ignore-platform-req(s)
#   deny  rm -r of var/, public/fileadmin/ or config/ (sites, system settings,
#         user files; var/cache, var/log and friends stay allowed)
#   ask   database:updateschema with destructive/remove/drop/*,
#         ddev delete, ddev import-db, DROP/TRUNCATE
# Everything else passes through. Reads the hook payload on stdin.

set -euo pipefail

payload="$(cat)"
cmd="$(jq -r '.tool_input.command // empty' <<<"$payload" 2>/dev/null || true)"
[[ -z "$cmd" ]] && exit 0

