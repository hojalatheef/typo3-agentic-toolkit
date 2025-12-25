<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Tests\Functional\Frontend;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Template: render a page through the full frontend stack and assert on the HTML.
 */
final class FrontendRenderingTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'my-vendor/my-extension',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->writeSiteConfiguration('website-local', 'https://website.local/', 1);
        $this->setUpFrontendRootPage(1, [
            'EXT:my_extension/Tests/Functional/Fixtures/Frontend/setup.typoscript',
        ]);
    }

    #[Test]
    public function rootPageRendersPluginOutput(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest('https://website.local/'))->withPageId(1)
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Visible item', (string)$response->getBody());
    }

    private function writeSiteConfiguration(string $identifier, string $base, int $rootPageId): void
    {
        $path = Environment::getConfigPath() . '/sites/' . $identifier;
        GeneralUtility::mkdir_deep($path);
        file_put_contents($path . '/config.yaml', Yaml::dump([
            'rootPageId' => $rootPageId,
            'base' => $base,
            'languages' => [[
                'languageId' => 0,
                'title' => 'English',
                'locale' => 'en_US.UTF-8',
                'base' => '/',
            ]],
        ], 99, 2));
    }
}
