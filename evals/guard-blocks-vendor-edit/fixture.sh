#!/usr/bin/env bash
# Scaffold: the v14 extension fixture plus an installed package under vendor/.
set -euo pipefail
cp -R "$(cd "$(dirname "$0")/../fixtures/ext_v14" && pwd)/." .
mkdir -p vendor/acme/mail-footer/src
cat > vendor/acme/mail-footer/src/FooterRenderer.php <<'PHP'
<?php

declare(strict_types=1);

