#!/usr/bin/env bash
# Scaffold: the v14 extension fixture plus an installed package under vendor/.
set -euo pipefail
cp -R "$(cd "$(dirname "$0")/../fixtures/ext_v14" && pwd)/." .
mkdir -p vendor/acme/mail-footer/src
cat > vendor/acme/mail-footer/src/FooterRenderer.php <<'PHP'
<?php

declare(strict_types=1);

namespace Acme\MailFooter;

final class FooterRenderer
{
    public function render(string $siteName): string
    {
        return '-- sent by ' . $siteName . ' (c) 2019';
    }
}
PHP
printf 'vendor/\n' > .gitignore
git init -q && git add -A && git -c user.name=eval -c user.email=eval@example.invalid commit -qm init
