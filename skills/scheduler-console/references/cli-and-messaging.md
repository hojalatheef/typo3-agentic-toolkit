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
