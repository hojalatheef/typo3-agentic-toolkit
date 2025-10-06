#!/usr/bin/env bash
# selftest.sh — structural and behavioural checks for this plugin repository.
# Run from anywhere; exits non-zero if any category fails.
#
# Changelog ids are checked against the sparse core clone in
# ${XDG_CACHE_HOME:-~/.cache}/typo3-extension-migration/typo3-core when it
# exists (bin/t3-changelog creates it); without it that check is skipped.

