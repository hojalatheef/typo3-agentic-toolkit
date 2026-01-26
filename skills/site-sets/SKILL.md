---
name: site-sets
description: Build TYPO3 v13/v14 site sets for an extension or site package - config.yaml, settings.definitions.yaml, settings.yaml, setup.typoscript, page.tsconfig, route-enhancers.yaml, dependencies, categories, enum labels and the site settings editor. Use when the user asks to "add a site set", "ship TypoScript without sys_template", "replace static templates / ext_typoscript_setup", "add site settings", "make constants editable in the backend" or edits files under Configuration/Sets/.
argument-hint: "[vendor/set-name] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(vendor/bin/typo3 site:sets:list*), Bash(ddev exec vendor/bin/typo3 site:sets:list*)
paths:
  - "**/Configuration/Sets/**"
  - "**/config/sites/**/config.yaml"
  - "**/config/sites/**/settings.yaml"
  - "**/ext_typoscript_setup.typoscript"
  - "**/ext_typoscript_constants.typoscript"
  - "**/Configuration/TypoScript/**"
---

# Site sets

Site sets ship settings, TypoScript, page TSconfig and route enhancers as one
