---
name: release
description: Prepare a release of a TYPO3 extension - bump the version in ext_emconf.php, composer.json (if it carries one) and Documentation/guides.xml, write the CHANGELOG entry, run the quality gate, create an annotated tag, and walk the TER and Packagist checklist. User-only command (/release <version>); never pushes or uploads without explicit confirmation.
argument-hint: "<version, e.g. 3.2.0>"
disable-model-invocation: true
user-invocable: true
allowed-tools: Read, Edit, Grep, Glob, Bash
---

# Release a TYPO3 extension

Version to release: $ARGUMENTS

You prepare everything locally. `git push`, `git push --tags`, TER uploads and Packagist updates happen only after the user types an explicit yes to the exact command you show.

## Current state

!`git describe --tags --abbrev=0 2>/dev/null || echo "no tags"`

!`git status --short 2>/dev/null | head -20 || true`

!`grep -E "'version'" ext_emconf.php 2>/dev/null || true; grep -o 'release="[^"]*"' Documentation/guides.xml 2>/dev/null || true; jq -r '.version // "composer.json has no version (good)"' composer.json 2>/dev/null || true`

## Preconditions

1. The version argument is valid SemVer and greater than the last tag. Stop otherwise.
