# Resource identifiers and URL generation

## Identifier syntax (Feature-107537, 14.0)

| Type | Identifier | Notes |
| --- | --- | --- |
| Package resource | `PKG:my-vendor/my-extension:Resources/Public/Icons/Extension.svg` | Composer name + path. Recommended for new code. |
| Package resource (legacy) | `EXT:my_extension/Resources/Public/Icons/Extension.svg` | Not deprecated yet. |
| App resource | `PKG:typo3/app:public/typo3temp/assets/style.css` | Use the real public dir name (`web`, `htdocs`, ...). |
| FAL resource | `FAL:1:/templates/css/main.css` | Storage UID + identifier. |
| URI resource | `https://www.example.com/my/image.svg`, `URI:/path/to/image.svg` | `URI:` value must be a valid URI or an exception is thrown. |

Deprecated legacy strings: `fileadmin/...` (default storage only), `typo3temp/assets/...`, `_assets/vite/...`.
Paths like `typo3conf/ext/my_ext/Resources/Public/Image.svg` no longer work consistently.

App resources default to `public/_assets`, `public/typo3temp/assets`, `public/uploads`. Extend them in
`config/system/resources.php` (Feature-109409); `FE.addAllowedPaths` is deprecated (Deprecation-109409).

Package resources may only point to `Configuration`, `Resources/Private`, `Resources/Public` or paths declared in
`Configuration/Resources.php`; anything else triggers a deprecation (Deprecation-109409).

## PHP

```php
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\SystemResource\Publishing\SystemResourcePublisherInterface;
use TYPO3\CMS\Core\SystemResource\Publishing\UriGenerationOptions;
use TYPO3\CMS\Core\SystemResource\SystemResourceFactory;

public function __construct(
    private readonly SystemResourceFactory $systemResourceFactory,
    private readonly SystemResourcePublisherInterface $resourcePublisher,
) {}

public function renderUrl(string $identifier, ServerRequestInterface $request): string
{
    $resource = $this->systemResourceFactory->createPublicResource($identifier);
    return (string)$this->resourcePublisher->generateUri(
        $resource,
        $request,
        new UriGenerationOptions(absoluteUri: true),
    );
}
```

`UriGenerationOptions` named arguments seen in the changelogs: `absoluteUri`, `uriPrefix`, `cacheBusting`.
`$request = null`: falls back to `$GLOBALS['TYPO3_REQUEST']`; on CLI no absolute URLs.

Replaces (Deprecation-107537, removal v15):
