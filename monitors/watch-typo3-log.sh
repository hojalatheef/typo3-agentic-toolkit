#!/usr/bin/env bash
# watch-typo3-log.sh — monitor: streams new TYPO3 log errors to Claude.
#
# Follows var/log/typo3_*.log (Composer mode) or typo3temp/var/log/typo3_*.log
# (classic mode) in the working directory and prints each new line of level
# ERROR, CRITICAL, ALERT or EMERGENCY, or that names an exception, one line
# per event. Exits quietly when the directory has no TYPO3 log.

set -euo pipefail

logs=()
for pattern in var/log/typo3_*.log typo3temp/var/log/typo3_*.log; do
  for f in $pattern; do [[ -f "$f" ]] && logs+=("$f"); done
done
(( ${#logs[@]} )) || { echo "no TYPO3 log under var/log or typo3temp/var/log; monitor idle"; exit 0; }

tail -F -n 0 "${logs[@]}" 2>/dev/null \
  | grep --line-buffered -E '\[(ERROR|CRITICAL|ALERT|EMERGENCY)\]|Exception' \
  | while IFS= read -r line; do printf 'TYPO3 log: %s\n' "${line:0:400}"; done
