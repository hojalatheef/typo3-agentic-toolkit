#!/usr/bin/env bash
# Scaffold: a copy of the tiny v14 extension fixture as a git repository.
set -euo pipefail
cp -R "$(cd "$(dirname "$0")/../fixtures/ext_v14" && pwd)/." .
git init -q && git add -A && git -c user.name=eval -c user.email=eval@example.invalid commit -qm init
