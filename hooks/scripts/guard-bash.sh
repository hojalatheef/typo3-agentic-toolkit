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

decide() {
  jq -n --arg d "$1" --arg r "typo3-agentic-toolkit: $2" '{
    hookSpecificOutput: {hookEventName: "PreToolUse", permissionDecision: $d, permissionDecisionReason: $r}
  }'
  exit 0
}
has() { command grep -Eiq -- "$1" <<<"$cmd"; }

if has 'git[[:space:]][^|;&]*--no-verify'; then
  decide deny "skipping git hooks is not allowed. Run what the hook runs, fix what it reports, then commit again."
fi
if has 'composer[[:space:]][^|;&]*--ignore-platform-req'; then
  decide deny "--ignore-platform-req(s) resolves for a PHP nobody runs. Set the PHP version where the site runs (e.g. php_version in .ddev/config.yaml) and resolve again."
fi
if has '(^|[;&|[:space:]])rm[[:space:]]+(-[a-z]*r|--recursive|-[a-z]+[[:space:]]+-[a-z]*r)[^|;&]*[[:space:]](\./)?(var/?(\*)?|public/fileadmin(/[^[:space:]]*)?|config(/[^[:space:]]*)?)([[:space:]]|$|;|&|\|)'; then
  decide deny "this deletes var/, user files in public/fileadmin/ or the project configuration in config/. Remove only what you mean (e.g. var/cache/*) or use 'bin/t3 cache:flush'."
fi
if has 'database:updateschema[^|;&]*(\*|remove|drop|destructive)' \
   || has 'ddev[[:space:]]+(delete|import-db)' \
   || has '(drop[[:space:]]+(database|schema|table)|truncate[[:space:]]+(table[[:space:]]+)?[a-z_`])'; then
  decide ask "this removes or replaces database content and git cannot bring it back. Confirm only with a current dump (ddev snapshot) and on a copy, not production."
fi
exit 0
