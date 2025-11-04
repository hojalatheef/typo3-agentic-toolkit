---
name: adr
description: Write an Architecture Decision Record for a TYPO3 project or extension under Documentation/Decisions/ in reStructuredText, numbered, with context, options, decision and consequences, citing TYPO3 changelog IDs for any core concept involved. Use when the user says "write an ADR", "record this decision", "document why we chose X", or after the typo3-architect plan contains a decision worth keeping.
argument-hint: "<decision title>"
context: fork
allowed-tools: Read, Grep, Glob, Write, Bash(ls:*), Bash(git log:*), Bash(t3-changelog:*)
---

# Architecture Decision Record

Decision: $ARGUMENTS

You run in a forked context. Gather what you need, write one ADR file, and reply with its path and a summary.
