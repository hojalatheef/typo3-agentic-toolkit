# CLI commands and messaging

## Command attributes (Feature-107151, 14.0)

```php
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use TYPO3\CMS\Core\Attribute\AsNonSchedulableCommand;

#[AsCommand('myextension:import', 'Import data from external source')]
#[AsNonSchedulableCommand]
final class ImportCommand extends Command {}
```

- `#[AsCommand]` alone: registered and schedulable.
- `#[AsNonSchedulableCommand]` (empty attribute) on top: the scheduler registry ignores the command.
- Replaces the `schedulable: false` tag attribute in `Services.yaml` / `Services.php` (still needed on 13.4).

## Shell completion (Feature-106839, 14.0)

`vendor/bin/typo3 completion` comes from `symfony/console`; shells: bash, fish, zsh.

```bash
# static
vendor/bin/typo3 completion bash | sudo tee /etc/bash_completion.d/typo3
# dynamic, in ~/.bashrc
eval "$(/var/www/html/vendor/bin/typo3 completion bash)"
```

Custom commands get value completion by passing a callback as the fifth argument of `addArgument()` /
`addOption()`:

```php
$this->addArgument(
    'names',
    InputArgument::IS_ARRAY,
    'Who do you want to greet?',
    null,
    function (CompletionInput $input): array {
        return ['jane', 'jon'];
    }
);
```

Core commands with argument completion: `redirects:cleanup`, `redirects:checkintegrity`,
`styleguide:generate`.

## System configuration commands (Feature-108815, 14.2)

Provided by `typo3/cms-lowlevel`. Paths use `/` separators.

| Command | Example |
| --- | --- |
