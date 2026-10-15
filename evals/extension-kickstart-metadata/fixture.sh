#!/usr/bin/env bash
# Scaffold: an empty git repository for a new standalone extension.
set -euo pipefail
git init -q
git -c user.name=eval -c user.email=eval@example.invalid commit -q --allow-empty -m init
