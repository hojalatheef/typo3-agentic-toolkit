# Encryption, HMAC and password hashing

## CipherService and KeyFactory (Feature-108002, 14.0)

`TYPO3\CMS\Core\Crypto\Cipher\CipherService` encrypts with XChaCha20-Poly1305 (AEAD, libsodium / `ext-sodium`).
Both services are autoconfigured; inject them.

| API | Purpose |
| --- | --- |
| `KeyFactory::deriveSharedKeyFromEncryptionKey(string $seed, int $subKeyId = 1)` | Key derived from `$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']`; one seed per purpose. |
| `KeyFactory::createSharedKeyFromString(string $key)` | Key from outside, e.g. `getenv('MY_APP_KEY')`. |
| `KeyFactory::generateSharedKey()` | Random key. |
| `CipherService::encrypt(string $plainText, SharedKey $key, string $additionalData = '')` | Returns `CipherValue`; `(string)` gives a base64url JSON with nonce and ciphertext. |
| `CipherService::decrypt(CipherValue $value, SharedKey $key, string $additionalData = '')` | Throws `CipherDecryptionFailedException` on wrong key, tampering or bad format. |
| `CipherValue::fromSerialized(string $value)` | Parse the stored string. |

The `$additionalData` parameter was read from the core source, not from the changelog. Use it to bind a ciphertext
to its context (e.g. table + uid) so it cannot be copied to another record.

```php
$key = $this->keyFactory->deriveSharedKeyFromEncryptionKey('my-extension-api-tokens');
$stored = (string)$this->cipherService->encrypt($apiToken, $key);

$plain = $this->cipherService->decrypt(CipherValue::fromSerialized($stored), $key);
```

Rules:

- Changing `encryptionKey` makes derived-key data undecryptable. Document this in the extension README.
- Ciphertexts are non-deterministic; you cannot `WHERE token = ...`. Store an HMAC next to it for lookups.
- Treat decrypted values as sensitive: no logging, no `DebuggerUtility::var_dump()`.

## HashService (Feature-102761, 13.0; Breaking-106307, 14.0)

`TYPO3\CMS\Core\Crypto\HashService` replaces `GeneralUtility::hmac()` (deprecated 13.1, Deprecation-102762, removed
14.0) and the Extbase `HashService` (Deprecation-102763, removed 14.0). Every call needs an additional secret so
an HMAC for one purpose cannot be replayed for another.

| Method | Purpose |
| --- | --- |
| `hmac(string $input, string $additionalSecret, HashAlgo $algo = HashAlgo::SHA1)` | Create an HMAC. |
| `appendHmac(string $string, string $additionalSecret, HashAlgo $algo = ...)` | Return string + HMAC. |
| `validateHmac(string $string, string $additionalSecret, string $hmac, HashAlgo $algo = ...)` | Constant-time check. |
| `validateAndStripHmac(string $string, string $additionalSecret, HashAlgo $algo = ...)` | Check and strip, throws on failure. |
