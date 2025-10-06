#!/usr/bin/env bash
# dev-companion-mcp.sh — starts the TYPO3 Dev Companion MCP server (stdio).
#
# Usage: dev-companion-mcp.sh <checkout>
#   <checkout>  git checkout of github.com/TYPO3/dev-companion with
#               `composer install` done (the plugin's dev_companion_path option)
#
# Runs `php <checkout>/bin/typo3-dev-companion` with a host PHP >= 8.2, else in
# a php:8.4-cli container that mounts the checkout and the working directory
# at their host paths. Without a checkout it explains on stderr and exits 1,
# so /mcp shows the server as failed instead of hanging.

set -euo pipefail

home="${1:-}"
server="$home/bin/typo3-dev-companion"
if [[ -z "$home" || ! -f "$server" ]]; then
  echo "typo3-dev-companion: set the plugin option dev_companion_path to a checkout of github.com/TYPO3/dev-companion (after composer install)." >&2
  exit 1
fi

