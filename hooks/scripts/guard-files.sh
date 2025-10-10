#!/usr/bin/env bash
# guard-files.sh — PreToolUse hook for Edit and Write.
#
#   ask   edits below vendor/, var/, public/typo3/, public/_assets/ (installed
#         or generated code: the next composer install or cache flush wipes
#         them). With the strict_guard option set to true this is a deny.
#   ask   config/system/settings.php (written by the Install Tool; prefer
#         config/system/additional.php or site settings)
# Everything else passes through. Reads the hook payload on stdin.

set -euo pipefail

