---
name: review
description: Review the current TYPO3 change (uncommitted work plus commits since the base branch) in a forked code-reviewer context against TYPO3 v14 practices - correctness, security, legacy APIs where a v13/v14 concept exists, silenced checks, missing tests. Use when the user says "review my changes", "review this diff", "is this ready to merge" or runs /review.
argument-hint: "[base-ref]"
context: fork
agent: code-reviewer
user-invocable: true
allowed-tools: Read, Grep, Glob, Bash(git diff:*), Bash(git log:*), Bash(git status:*), Bash(git merge-base:*), Bash(t3-changelog:*)
---

# Review the current change

Review the change below as `code-reviewer`. You run in a forked context: the user sees only your final reply, so put everything in it.

## Change under review

Base ref argument (empty means: merge base with `main`, else `master`): $ARGUMENTS

Working tree status:

!`git status --short 2>/dev/null | head -50 || true`

Diff stat against the base:

!`b="$(git merge-base HEAD origin/main 2>/dev/null || git merge-base HEAD main 2>/dev/null || git merge-base HEAD master 2>/dev/null || echo HEAD)"; echo "base: $b"; git diff --stat "$b" 2>/dev/null | tail -40 || true`

Commits since the base:

!`git log --oneline -20 "$(git merge-base HEAD origin/main 2>/dev/null || git merge-base HEAD main 2>/dev/null || echo HEAD~1)..HEAD" 2>/dev/null || true`
