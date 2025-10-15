#!/usr/bin/env bash
# subagent-log.sh — SubagentStart and SubagentStop hook.
#
# SubagentStart stores the start time per agent id; SubagentStop appends one
# tab-separated line to ${CLAUDE_PLUGIN_DATA}/agents.log: finish time (UTC),
# session, agent type, agent id, duration in seconds ("-" when the start was
# not seen). Reads the hook payload on stdin; prints nothing.

