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
