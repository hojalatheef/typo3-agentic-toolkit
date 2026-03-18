<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Security;

use TYPO3\CMS\Core\Crypto\Cipher\CipherDecryptionFailedException;
use TYPO3\CMS\Core\Crypto\Cipher\CipherService;
use TYPO3\CMS\Core\Crypto\Cipher\CipherValue;
use TYPO3\CMS\Core\Crypto\Cipher\KeyFactory;
use TYPO3\CMS\Core\Crypto\HashAlgo;
use TYPO3\CMS\Core\Crypto\HashService;

/**
 * Stores third-party API tokens encrypted (Feature-108002, TYPO3 ^14.0) and
 * provides a deterministic HMAC for lookups (Feature-102761, Breaking-106307).
 *
 * Changing $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] makes stored
 * values undecryptable.
 */
final readonly class TokenVault
{
    private const KEY_SEED = 'my-extension-api-tokens';
    private const HMAC_SECRET = 'my-extension-api-token-lookup';

    public function __construct(
        private CipherService $cipherService,
        private KeyFactory $keyFactory,
        private HashService $hashService,
    ) {}

    /**
     * @param string $context Binds the ciphertext to its record, e.g. "tx_myext_account:42"
     */
    public function encrypt(#[\SensitiveParameter] string $token, string $context): string
    {
        $key = $this->keyFactory->deriveSharedKeyFromEncryptionKey(self::KEY_SEED);
        return (string)$this->cipherService->encrypt($token, $key, $context);
    }

    public function decrypt(string $stored, string $context): ?string
    {
        $key = $this->keyFactory->deriveSharedKeyFromEncryptionKey(self::KEY_SEED);
        try {
            return $this->cipherService->decrypt(CipherValue::fromSerialized($stored), $key, $context);
        } catch (CipherDecryptionFailedException) {
            // wrong key, tampered value or copied to another record
            return null;
        }
    }

    /**
     * Deterministic fingerprint to find a record by token without decrypting all rows.
     */
    public function fingerprint(#[\SensitiveParameter] string $token): string
    {
        return $this->hashService->hmac($token, self::HMAC_SECRET, HashAlgo::SHA3_256);
    }

    public function matches(#[\SensitiveParameter] string $token, string $fingerprint): bool
    {
        return $this->hashService->validateHmac($token, self::HMAC_SECRET, $fingerprint, HashAlgo::SHA3_256);
    }
}
