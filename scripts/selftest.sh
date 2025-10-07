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

echo "frontmatter"
for f in skills/*/SKILL.md; do
  head -1 "$f" | grep -q '^---$' || { bad "$f: no frontmatter"; continue; }
  meta="$(fm "$f")"
  name="$(sed -n 's/^name: *//p' <<<"$meta" | head -1)"
  [[ "$name" == "$(basename "$(dirname "$f")")" ]] || bad "$f: name '$name' differs from its folder"
  long_desc "$meta" || bad "$f: description missing or shorter than 40 characters"
done
for f in agents/*.md; do
  head -1 "$f" | grep -q '^---$' || { bad "$f: no frontmatter"; continue; }
  meta="$(fm "$f")"
  grep -q '^name: ' <<<"$meta" || bad "$f: name missing"
  long_desc "$meta" || bad "$f: description missing or too short"
  grep -Eq '^model: (opus|sonnet|haiku|inherit|claude-)' <<<"$meta" || bad "$f: model missing"
  grep -Eq '^(hooks|mcpServers|permissionMode):' <<<"$meta" && bad "$f: plugin agents ignore hooks/mcpServers/permissionMode"
done
for f in output-styles/*.md; do
  meta="$(fm "$f")"
  grep -q '^name: ' <<<"$meta" && long_desc "$meta" 20 || bad "$f: name or description missing"
done
n_skills=(skills/*/SKILL.md); n_agents=(agents/*.md); n_styles=(output-styles/*.md)
ok "checked ${#n_skills[@]} skills, ${#n_agents[@]} agents, ${#n_styles[@]} output styles"

echo "relative links"
broken=0
while IFS= read -r -d '' f; do
  while IFS= read -r link; do
    target="${link%%#*}"
    [[ -z "$target" || "$target" =~ ^[a-z]+: ]] && continue
    [[ -e "$(dirname "$f")/$target" ]] || { bad "$f links to missing $target"; broken=1; }
  done < <(grep -oE '\]\([^)[:space:]]+\)' "$f" | sed -E 's/^\]\(//; s/\)$//')
done < <(find skills agents output-styles -name '*.md' -print0 2>/dev/null)
(( broken )) || ok "every relative link under skills/, agents/ and output-styles/ resolves"

echo "changelog ids"
changelog="${XDG_CACHE_HOME:-$HOME/.cache}/typo3-extension-migration/typo3-core/typo3/sysext/core/Documentation/Changelog"
if [[ -d "$changelog" ]]; then
  known="$(find "$changelog" -name '*.rst' -exec basename {} \; | grep -oE '^(Feature|Breaking|Deprecation|Important)-[0-9]+' | sort -u)"
  missing=0; count=0
  while IFS= read -r id; do
    count=$((count + 1))
    grep -qx "$id" <<<"$known" || { bad "cited $id has no changelog entry"; missing=1; }
  done < <(grep -rhoE '(Feature|Breaking|Deprecation|Important)-[0-9]{5,6}' skills agents output-styles hooks docs README.md CATALOG.md evals 2>/dev/null | sort -u)
  (( missing )) || ok "all $count cited changelog ids exist"
else skip "no core changelog cache (run bin/t3-changelog once)"; fi

echo "scripts"
sh_files=(hooks/scripts/*.sh monitors/*.sh scripts/*.sh bin/t3 bin/t3-changelog evals/*/fixture.sh)
for s in "${sh_files[@]}"; do
  [[ -x "$s" ]] || bad "$s is not executable"
  bash -n "$s" || bad "bash -n $s"
done
ok "bash -n and executable bits (${#sh_files[@]} scripts)"
if command -v shellcheck >/dev/null; then
  shellcheck -S warning "${sh_files[@]}" && ok "shellcheck" || bad "shellcheck"
else skip "shellcheck not installed"; fi

