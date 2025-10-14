#!/usr/bin/env bash
# session-context.sh — SessionStart and FileChanged (composer.lock) hook.
#
# Detects the TYPO3 setup in the working directory and hands Claude a short
# summary: core version, Composer or classic mode, extension or project, DDEV
# and its PHP version, and how many site sets exist. On SessionStart the
# summary goes in as additionalContext (and TYPO3_* variables into
# $CLAUDE_ENV_FILE when set, plus a watch on composer.lock); on FileChanged
# it is a systemMessage.
# Prints nothing outside a TYPO3 directory. Reads the hook payload on stdin.

set -euo pipefail

