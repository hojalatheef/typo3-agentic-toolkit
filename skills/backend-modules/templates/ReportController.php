<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Controller\Backend;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\ComponentFactory;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * TYPO3 ^14.0: #[AsController] (Feature-102631), ComponentFactory (Feature-107823),
 * automatic reload/shortcut buttons (Feature-108008), language selector (Feature-107875).
 */
#[AsController]
final readonly class ReportController
{
    public function __construct(
        private ModuleTemplateFactory $moduleTemplateFactory,
        private ComponentFactory $componentFactory,
        private UriBuilder $uriBuilder,
        private IconFactory $iconFactory,
        private SiteFinder $siteFinder,
    ) {}

    public function indexAction(ServerRequestInterface $request): ResponseInterface
    {
        $pageId = (int)($request->getQueryParams()['id'] ?? 0);
        $view = $this->createView($request, $pageId, 'Report overview');

        $view->assignMultiple([
            'pageId' => $pageId,
            'detailUri' => (string)$this->uriBuilder->buildUriFromRoute(
                'my_extension_tools_report.detail',
                ['id' => $pageId]
            ),
        ]);

        return $view->renderResponse('Backend/Report/Index');
    }

    public function detailAction(ServerRequestInterface $request): ResponseInterface
    {
        $pageId = (int)($request->getQueryParams()['id'] ?? 0);
        $view = $this->createView($request, $pageId, 'Report detail');

        $returnUrl = (string)$this->uriBuilder->buildUriFromRoute('my_extension_tools_report', ['id' => $pageId]);
        $view->addButtonToButtonBar(
            $this->componentFactory->createBackButton($returnUrl),
            ButtonBar::BUTTON_POSITION_LEFT,
            1
        );
        $view->addButtonToButtonBar(
            $this->componentFactory->createLinkButton()
                ->setHref((string)$this->uriBuilder->buildUriFromRoute(
                    'my_extension_tools_report.detail',
                    ['id' => $pageId, 'export' => 'csv']
                ))
                ->setTitle('Export CSV')
                ->setShowLabelText(true)
                ->setIcon($this->iconFactory->getIcon('actions-download', IconSize::SMALL)),
            ButtonBar::BUTTON_POSITION_LEFT,
            2
        );

        return $view->renderResponse('Backend/Report/Detail');
    }

    public function reviewAction(ServerRequestInterface $request): ResponseInterface
    {
        return $this->createView($request, 0, 'Review')->renderResponse('Backend/Report/Review');
    }

    public function adminAction(ServerRequestInterface $request): ResponseInterface
    {
        return $this->createView($request, 0, 'Administration')->renderResponse('Backend/Report/Admin');
    }

    private function createView(ServerRequestInterface $request, int $pageId, string $title): ModuleTemplate
    {
        $view = $this->moduleTemplateFactory->create($request);
        $view->setTitle($title);

        // Module actions dropdown from the module configuration (hidden if only one entry).
        $view->makeDocHeaderModuleMenu(['id' => $pageId]);

        // Reload + shortcut buttons are added automatically; only describe the shortcut.
        $module = $request->getAttribute('module');
        $view->getDocHeaderComponent()->setShortcutContext(
            routeIdentifier: $module?->getIdentifier() ?? 'my_extension_tools_report',
            displayName: $title,
            arguments: ['id' => $pageId]
        );

        if ($pageId > 0) {
            $this->addLanguageSelector($view, $request, $pageId);
        }

        return $view;
    }

    private function addLanguageSelector(ModuleTemplate $view, ServerRequestInterface $request, int $pageId): void
    {
        try {
            $site = $this->siteFinder->getSiteByPageId($pageId);
        } catch (\Throwable) {
            return;
        }
        $currentLanguageId = (int)($request->getQueryParams()['language'] ?? 0);
        $module = $request->getAttribute('module');
        $route = $module?->getIdentifier() ?? 'my_extension_tools_report';

        $dropdown = $this->componentFactory->createDropDownButton()
            ->setLabel($this->getLanguageService()->sL('core.core:labels.language'))
            ->setShowLabelText(true)
            ->setShowActiveLabelText(true);

        foreach ($site->getLanguages() as $siteLanguage) {
            $languageId = $siteLanguage->getLanguageId();
            $dropdown->addItem(
                $this->componentFactory->createDropDownRadio()
                    ->setHref((string)$this->uriBuilder->buildUriFromRoute($route, [
                        'id' => $pageId,
                        'language' => $languageId,
                    ]))
                    ->setLabel($siteLanguage->getTitle())
                    ->setIcon($this->iconFactory->getIcon($siteLanguage->getFlagIdentifier()))
                    ->setActive($currentLanguageId === $languageId)
            );
        }

        $view->getDocHeaderComponent()->setLanguageSelector($dropdown);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
