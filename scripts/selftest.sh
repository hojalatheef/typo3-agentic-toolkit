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

