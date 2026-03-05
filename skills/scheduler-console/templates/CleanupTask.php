<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Task;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Scheduler task as a TCA record type of tx_scheduler_task (Feature-107526, TYPO3 ^14.0).
 * Fields: number_of_days (core column) and tx_myextension_table (own column),
 * see Configuration/TCA/Overrides/tx_scheduler_task_cleanup.php.
 */
class CleanupTask extends AbstractTask
{
    protected int $days = 30;

    protected string $table = '';

    public function execute(): bool
    {
        if ($this->table === '' || !isset($GLOBALS['TCA'][$this->table]['ctrl']['tstamp'])) {
            return false;
        }

        $tstampField = $GLOBALS['TCA'][$this->table]['ctrl']['tstamp'];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->delete($this->table)
            ->where(
                $queryBuilder->expr()->lt(
                    $tstampField,
                    $queryBuilder->createNamedParameter(time() - $this->days * 86400, Connection::PARAM_INT)
                )
            )
            ->executeStatement();

        return true;
    }

    /**
     * Called by the upgrade wizard to move values of an old serialized task into columns.
     */
    public function getTaskParameters(): array
    {
        return [
            'number_of_days' => $this->days,
            'tx_myextension_table' => $this->table,
        ];
    }

    /**
     * Receives the full tx_scheduler_task record for TCA-based tasks.
     * Accept legacy keys (old AdditionalFieldProvider names) as fallback.
     */
    public function setTaskParameters(array $parameters): void
    {
        $this->days = (int)($parameters['number_of_days'] ?? $parameters['myextension_days'] ?? 30);
        $this->table = (string)($parameters['tx_myextension_table'] ?? $parameters['myextension_table'] ?? '');
    }

    public function getAdditionalInformation(): string
    {
        return sprintf('%s, older than %d days', $this->table, $this->days);
    }
}
