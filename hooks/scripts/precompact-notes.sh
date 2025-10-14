#!/usr/bin/env bash
# precompact-notes.sh — PreCompact hook.
#
# Before the context is compacted, appends the facts worth keeping to
# ${CLAUDE_PLUGIN_DATA}/notes/<session>.md: time, trigger, working
# directory, TYPO3 version, git branch and the files changed so far. The
# notes survive the compaction; read them back when the summary is thin.
# Reads the hook payload on stdin; prints nothing.

set -euo pipefail

