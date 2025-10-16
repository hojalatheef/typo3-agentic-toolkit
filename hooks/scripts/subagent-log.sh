#!/usr/bin/env bash
# subagent-log.sh — SubagentStart and SubagentStop hook.
#
# SubagentStart stores the start time per agent id; SubagentStop appends one
# tab-separated line to ${CLAUDE_PLUGIN_DATA}/agents.log: finish time (UTC),
# session, agent type, agent id, duration in seconds ("-" when the start was
# not seen). Reads the hook payload on stdin; prints nothing.

set -euo pipefail

payload="$(cat)"
field() { jq -r "$1 // empty" <<<"$payload" 2>/dev/null || true; }
event="$(field .hook_event_name)"
session="$(field .session_id)"
agent="$(field .agent_type)"
id="$(field .agent_id)"
safe_id="${id//[^A-Za-z0-9_-]/_}"

dir="${CLAUDE_PLUGIN_DATA:-${XDG_DATA_HOME:-$HOME/.local/share}/typo3-agentic-toolkit}"
mkdir -p "$dir/agents-running"
now="$(date -u +%s)"

