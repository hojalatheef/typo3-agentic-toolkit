#!/usr/bin/env bash
# php-lint.sh — PostToolUse hook (Edit|Write, runs async) for *.php files.
#
# Runs `php -l` on the written file with the runner the php_runner option
# names: auto (DDEV when .ddev/ exists and the project runs, then host php,
# then a php:8.4-cli container), ddev, host or docker. A syntax error goes
# back to Claude (decision block + additionalContext); a clean file and a
# missing PHP stay silent. Reads the hook payload on stdin.

set -euo pipefail

