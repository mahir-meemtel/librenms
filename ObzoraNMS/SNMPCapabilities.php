<?php
namespace ObzoraNMS;

use App\Facades\ObzoraConfig;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SNMPCapabilities
{
    /**
     * @var bool
     */
    private static $sha2;
    /**
     * @var bool
     */
    private static $aes256;

    public static function supportsSHA2(): bool
    {
        if (self::$sha2 === null) {
            self::detectCapabilities();
        }

        return self::$sha2;
    }

    public static function supportsAES256(): bool
    {
        if (self::$aes256 === null) {
            self::detectCapabilities();
        }

        return self::$aes256;
    }

    public static function supportedAuthAlgorithms(): array
    {
        return array_keys(array_filter(self::authAlgorithms()));
    }

    public static function supportedCryptoAlgorithms(): array
    {
        return array_keys(array_filter(self::cryptoAlgoritms()));
    }

    public static function authAlgorithms(): array
    {
        $sha2 = self::supportsSHA2();

        return [
            'SHA' => true,
            'SHA-224' => $sha2,
            'SHA-256' => $sha2,
            'SHA-384' => $sha2,
            'SHA-512' => $sha2,
            'MD5' => true,
        ];
    }

    public static function cryptoAlgoritms(): array
    {
        $aes256 = self::supportsAES256();

        return [
            'AES' => true,
            'AES-192' => $aes256,
            'AES-256' => $aes256,
            'AES-256-C' => $aes256,
            'DES' => true,
        ];
    }

    private static function detectCapabilities(): void
    {
        $process = new Process([ObzoraConfig::get('snmpget', 'snmpget'), '--help']);
        $process->run();

        self::$sha2 = Str::contains($process->getErrorOutput(), 'SHA-512');
        self::$aes256 = Str::contains($process->getErrorOutput(), 'AES-256');
    }
}
