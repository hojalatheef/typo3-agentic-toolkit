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
| `configuration:show` | `vendor/bin/typo3 configuration:show SYS/sitename --type=active --json` (`--type=local` for `settings.php` only; default shows a diff when overridden) |
| `configuration:set` | `vendor/bin/typo3 configuration:set BE/debug true --json` |
| `configuration:remove` | `vendor/bin/typo3 configuration:remove "EXTCONF/ext1,EXTCONF/ext2" --force` |

`configuration:set` writes `config/system/settings.php`; only paths from the default configuration or allowed
roots (`EXTENSIONS`, `EXTCONF`, `DB`) are writable. `--json` gives typed values (`true`, `1`, `{"key": "value"}`).
Overrides in `config/system/additional.php` win at runtime - check with `--type=active`.

## Messenger (Feature-101700, 13.0; Feature-106261, 14.2)

Handlers are registered by attribute (class or method level); no `messenger.message_handler` tag needed:

```php
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DemoHandler
{
    public function __invoke(DemoMessage $message): void {}
}
```

`messenger:consume` options aligned with Symfony in 14.2:

| Option | Meaning |
| --- | --- |
| `--limit` / `-l` | Stop after N messages. |
| `--failure-limit` / `-f` | Stop after N failed messages. |
| `--memory-limit` / `-m` | Stop above memory, e.g. `128M`. |
| `--time-limit` / `-t` | Stop after N seconds. |
| `--bus` / `-b` | Bus to dispatch received messages to. |
| `--all` | Consume all receivers. |
| `--keepalive` | Use transport keepalive if supported. |

```bash
vendor/bin/typo3 messenger:consume my_receiver --time-limit=240 --memory-limit=128M
vendor/bin/typo3 messenger:consume --all
```
