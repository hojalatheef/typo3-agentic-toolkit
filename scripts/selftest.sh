#!/usr/bin/env bash
# selftest.sh — structural and behavioural checks for this plugin repository.
# Run from anywhere; exits non-zero if any category fails.
#
# Changelog ids are checked against the sparse core clone in
# ${XDG_CACHE_HOME:-~/.cache}/typo3-extension-migration/typo3-core when it
# exists (bin/t3-changelog creates it); without it that check is skipped.

set -uo pipefail
root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$root" || exit 2
fixture="evals/fixtures/ext_v14"
fail=0
ok()   { printf '  ok    %s\n' "$1"; }
bad()  { printf '  FAIL  %s\n' "$1"; fail=1; }
skip() { printf '  skip  %s\n' "$1"; }
fm()   { awk 'NR==1{next} /^---$/{exit} {print}' "$1"; }
shopt -s nullglob

echo "manifests"
for j in .claude-plugin/plugin.json .claude-plugin/marketplace.json hooks/hooks.json monitors/monitors.json .mcp.json .lsp.json settings.json package.json; do
  [[ -f "$j" ]] || { bad "$j missing"; continue; }
  jq -e . "$j" >/dev/null 2>&1 && ok "$j is valid JSON" || bad "$j is not valid JSON"
done
v_plugin="$(jq -r .version .claude-plugin/plugin.json)"
[[ "$(jq -r .version package.json)" == "$v_plugin" ]] && ok "package.json version matches plugin.json ($v_plugin)" || bad "package.json version differs from plugin.json"
if [[ -f CHANGELOG.md ]]; then
  grep -q "## \[$v_plugin\]" CHANGELOG.md && ok "CHANGELOG has $v_plugin" || bad "CHANGELOG lacks entry for $v_plugin"
else skip "CHANGELOG.md not written yet"; fi
for m in $(jq -r '.modules[]?' hooks/hooks.json); do
  [[ -f "hooks/$m" ]] && ok "hooks module $m exists" || bad "hooks module $m missing"
done
while IFS= read -r cmd; do
  f="${cmd//\$\{CLAUDE_PLUGIN_ROOT\}\//}"; f="${f//\"/}"; f="${f%% *}"
  [[ -x "$f" ]] || bad "hook command $f is not an executable file"
done < <(jq -r '.. | objects | select(.type? == "command") | .command' hooks/hooks.json monitors/monitors.json)
ok "hook and monitor commands point at executable files"

# description text, also when written as a folded block (description: >-)
desc() { awk '/^description:/{sub(/^description: *(>-|>|\|)? */,""); d=$0; on=1; next} on && /^[[:space:]]/{d=d" "$0; next} {on=0} END{print d}' <<<"$1" | tr -s ' '; }
long_desc() { (( $(desc "$1" | wc -c) > ${2:-40} )); }

