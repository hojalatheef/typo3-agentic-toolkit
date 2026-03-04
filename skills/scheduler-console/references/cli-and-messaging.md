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
