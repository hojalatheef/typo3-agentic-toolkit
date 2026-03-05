<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Command;

use MyVendor\MyExtension\Message\ImportMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Schedulable by default: the scheduler lists it under "Execute console commands".
 * Add #[\TYPO3\CMS\Core\Attribute\AsNonSchedulableCommand] (TYPO3 ^14.0,
 * Feature-107151) if it must only run interactively.
 *
 * Shell completion for the "source" argument (Feature-106839):
 *   vendor/bin/typo3 completion bash > completion.sh && source completion.sh
 */
#[AsCommand('myextension:import', 'Queue an import from an external source')]
final class ImportCommand extends Command
{
    private const SOURCES = ['products', 'stores', 'prices'];

    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Source to import (' . implode(', ', self::SOURCES) . ')',
                null,
                fn(CompletionInput $input): array => array_values(array_filter(
                    self::SOURCES,
                    static fn(string $source): bool => str_starts_with($source, $input->getCompletionValue()),
                )),
            )
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Only report what would be queued');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $source = (string)$input->getArgument('source');

        if (!in_array($source, self::SOURCES, true)) {
            $io->error(sprintf('Unknown source "%s".', $source));
            return Command::INVALID;
        }

        if ($input->getOption('dry-run')) {
            $io->note(sprintf('Would queue import of "%s".', $source));
            return Command::SUCCESS;
        }

        // Handled by ImportMessageHandler; route ImportMessage to an async transport
        // and consume it with: vendor/bin/typo3 messenger:consume <receiver> --time-limit=240
        $this->bus->dispatch(new ImportMessage($source));
        $io->success(sprintf('Queued import of "%s".', $source));

        return Command::SUCCESS;
    }
}
