<?php

declare(strict_types=1);

// EXT:my_sitepackage/Classes/EventListener/RecordCreationListener.php
// Custom field transformation for Record objects (Feature-104846, 13.3).
// Turns the "coordinates" field of CType "maps" into a value object.

namespace MyVendor\MySitepackage\EventListener;

use MyVendor\MySitepackage\Domain\Coordinates;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Domain\Event\RecordCreationEvent;

final class RecordCreationListener
{
    #[AsEventListener]
    public function __invoke(RecordCreationEvent $event): void
    {
        $rawRecord = $event->getRawRecord();
        if ($rawRecord->getMainType() === 'tt_content'
            && $rawRecord->getRecordType() === 'maps'
            && $event->hasProperty('coordinates')
        ) {
            $event->setProperty('coordinates', new Coordinates($event->getProperty('coordinates')));
        }
    }
}
