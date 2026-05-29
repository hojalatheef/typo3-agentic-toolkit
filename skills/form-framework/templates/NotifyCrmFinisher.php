<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Domain\Finishers;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;
use TYPO3\CMS\Form\Domain\Model\FormElements\FileUpload;

/**
 * Sends submitted values and uploaded file names to a CRM endpoint.
 * Registered in Configuration/Form/MyFormSet/config.yaml (finishersDefinition.NotifyCrm).
 * Handles single and multiple uploads (Feature-105708, TYPO3 ^14.2).
 */
class NotifyCrmFinisher extends AbstractFinisher
{
    protected $defaultOptions = [
        'endpoint' => '',
    ];

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly LoggerInterface $logger,
    ) {}

    protected function executeInternal()
    {
        $endpoint = (string)$this->parseOption('endpoint');
        if ($endpoint === '') {
            return null;
        }

        $formRuntime = $this->finisherContext->getFormRuntime();
        $payload = ['fields' => [], 'files' => []];

        foreach ($formRuntime->getFormDefinition()->getRenderablesRecursively() as $element) {
            $value = $formRuntime[$element->getIdentifier()];

            if ($element instanceof FileUpload) {
                // Single upload: FileReference; multiple: ObjectStorage<FileReference>
                $files = $value instanceof ObjectStorage ? $value : [$value];
                foreach ($files as $file) {
                    if ($file instanceof FileReference) {
                        $payload['files'][] = $this->describeFile($file->getOriginalResource());
                    }
                }
                continue;
            }

            if (is_scalar($value)) {
                $payload['fields'][$element->getIdentifier()] = (string)$value;
            }
        }

        try {
            $this->requestFactory->request($endpoint, 'POST', [
                'json' => $payload,
                'timeout' => 5,
            ]);
        } catch (\Throwable $e) {
            // Do not break the submission for the visitor; log for the integrator.
            $this->logger->error('CRM notification failed', ['exception' => $e]);
        }

        return null;
    }

    private function describeFile(FileInterface $file): array
    {
        return [
            'name' => $file->getName(),
            'size' => $file->getSize(),
            'mimeType' => $file->getMimeType(),
        ];
    }
}
