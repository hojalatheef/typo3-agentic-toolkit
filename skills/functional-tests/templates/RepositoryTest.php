<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Tests\Functional\Domain\Repository;

use MyVendor\MyExtension\Domain\Model\Item;
use MyVendor\MyExtension\Domain\Repository\ItemRepository;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Template: Extbase repository test against CSV fixtures.
 * Replace MyVendor\MyExtension, my-vendor/my-extension and the table name.
 */
final class ItemRepositoryTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'my-vendor/my-extension',
    ];

    private ItemRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/pages.csv');

        // Extbase reads its configuration through the current request.
        $request = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $this->get(ConfigurationManagerInterface::class)->setRequest($request);

        $this->subject = $this->get(ItemRepository::class);
        $querySettings = $this->get(Typo3QuerySettings::class);
        $querySettings->setStoragePageIds([2]);
        $this->subject->setDefaultQuerySettings($querySettings);
    }

    #[Test]
    public function findAllReturnsOnlyVisibleRecords(): void
    {
        $titles = array_map(
            static fn(Item $item): string => $item->getTitle(),
            $this->subject->findAll()->toArray(),
        );

        self::assertSame(['Visible item'], $titles);
    }

    #[Test]
    public function addPersistsRecord(): void
    {
        $item = new Item();
        $item->setTitle('New item');
        $item->setPid(2);

        $this->subject->add($item);
        $this->get(PersistenceManagerInterface::class)->persistAll();

        $this->assertCSVDataSet(__DIR__ . '/../../Fixtures/Expected/AddPersistsRecord.csv');
    }
}
