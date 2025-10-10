#!/usr/bin/env bash
# legacy-api-check.sh — PostToolUse hook for Edit and Write.
#
# Greps the file just written for APIs that TYPO3 v13/v14 deprecated or
# removed and hands Claude a hint per hit, citing the core changelog entry
# (read it with bin/t3-changelog <id>). Silent when nothing matches.
# Reads the hook payload on stdin.

