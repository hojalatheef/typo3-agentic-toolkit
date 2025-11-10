---
name: content-blocks
description: Decide between the friendsoftypo3/content-blocks extension (not TYPO3 core) and plain TCA + site sets, and scaffold a Content Block - folder layout, config.yaml basics, make:content-block command, templates and labels. Use when the user asks "should I use Content Blocks", "create a content element with Content Blocks", "make:content-block", "config.yaml for a content block" or edits files under ContentBlocks/.
argument-hint: "[vendor/element-name] [extension path]"
allowed-tools: Read, Grep, Glob, Edit, Write, Bash(vendor/bin/typo3 make:content-block*), Bash(ddev exec vendor/bin/typo3 make:content-block*), Bash(composer show friendsoftypo3/content-blocks*)
paths:
  - "**/ContentBlocks/**"
---

# Content Blocks

Content Blocks is a **community extension**, `friendsoftypo3/content-blocks`,
maintained by the TYPO3 Structured Content Initiative. It is **not part of
TYPO3 core**. It generates TCA, database fields, the new content element
wizard entry, labels and the frontend/backend template wiring from one YAML
file per element.
