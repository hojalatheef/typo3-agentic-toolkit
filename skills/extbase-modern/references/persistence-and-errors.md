# Extbase persistence, rate limiter factory and error handling

## Unified RateLimiterFactory (Feature-109080, 14.2)

`TYPO3\CMS\Core\RateLimiter\RateLimiterFactory` is the single entry point for rate limiting (backend/frontend
login, password recovery, Extbase `#[RateLimit]`, custom code). Type-hint
`TYPO3\CMS\Core\RateLimiter\RateLimiterFactoryInterface`.

Request-scoped limiter keyed by the client IP:

```php
public function __construct(
    private readonly RateLimiterFactoryInterface $rateLimiterFactory,
) {}

public function doSomething(ServerRequestInterface $request): void
{
    $limiter = $this->rateLimiterFactory->createRequestBasedLimiter(
        $request,
        [
            'id' => 'my-extension-action',
            'policy' => 'sliding_window',
            'limit' => 10,
            'interval' => '1 hour',
        ],
    );
    if (!$limiter->consume()->isAccepted()) {
        // reject
    }
}
```

Custom key (e.g. a user ID): `$this->rateLimiterFactory->createLimiter([...config...], $userId)`.

Named service in `Services.yaml`, then call `create()` on it:

```yaml
myRateLimiter:
  class: TYPO3\CMS\Core\RateLimiter\RateLimiterFactory
  arguments:
    $config:
      id: 'my-custom-limiter'
      policy: 'sliding_window'
      limit: 5
      interval: '10 minutes'
```

Admin overrides by limiter ID in `config/system/additional.php`:

```php
$GLOBALS['TYPO3_CONF_VARS']['SYS']['rateLimiter']['login-be'] = [
    'limit' => 3,
    'interval' => '5 minutes',
];
```
