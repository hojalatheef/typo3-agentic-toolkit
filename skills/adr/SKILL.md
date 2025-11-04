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

## Existing records

!`ls Documentation/Decisions/ 2>/dev/null | tail -20 || echo "no Documentation/Decisions/ yet"`

## Steps

1. Pick the next number: highest existing `NNNN-*.rst` plus one, four digits, starting at `0001`.
2. Collect context from the conversation, `.typo3-toolkit/plan.md` (section "Risks and decisions") and the code. Ask nothing; mark unknowns as "to be confirmed".
3. If a TYPO3 core concept is involved, read its changelog entry with `t3-changelog <id or keyword>` and cite it as `Feature-<id>` with version. Do not describe core behaviour that the entry does not state.
4. List at least two real options, including "keep the current approach" when there is one.
5. Write `Documentation/Decisions/NNNN-<kebab-title>.rst` from the template in [references/adr-template.md](references/adr-template.md).
