<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use phpseclib4\Crypt\EC\PrivateKey;
use phpseclib4\Crypt\PublicKeyLoader;
use RuntimeException;
use Throwable;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    public static function caPrivateKey(bool $getShared = true): object
    {
        if ($getShared) {
            return static::getSharedInstance('caPrivateKey');
        }

        $keyPath = env("CA_PRIVATE_KEY_PATH");

        if (empty($keyPath) || !is_file($keyPath) || !is_readable($keyPath)) {
            throw new RuntimeException(
                "Unable to access CA's private key. Check CA_PRIVATE_KEY_PATH env variable and file permissions"
            );
        }

        $rawKey = file_get_contents($keyPath);

        if ($rawKey === false || trim($rawKey) === '') {
            throw new RuntimeException("CA's private key file is empty or corrupted.");
        }

        try {
            $passphrase = env('CA_PRIVATE_KEY_PASSPHRASE') ?: false;
            $privateKey = PublicKeyLoader::loadPrivateKey($rawKey, $passphrase);
        } catch (Throwable $e) {
            throw new RuntimeException('Nie udało się załadować/odszyfrować klucza prywatnego CA: ' . $e->getMessage(), 0, $e);
        } finally {
            if (is_string($rawKey) && function_exists('sodium_memzero')) {
                sodium_memzero($rawKey);
            }
        }

        return $privateKey;
    }
}
