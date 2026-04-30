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
```
