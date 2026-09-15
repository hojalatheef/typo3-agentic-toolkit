#!/usr/bin/env bash
# watch-typo3-log.sh — monitor: streams new TYPO3 log errors to Claude.
#
# Follows var/log/typo3_*.log (Composer mode) or typo3temp/var/log/typo3_*.log
# (classic mode) in the working directory and prints each new line of level
# ERROR, CRITICAL, ALERT or EMERGENCY, or that names an exception, one line
# per event. Exits quietly when the directory has no TYPO3 log.

