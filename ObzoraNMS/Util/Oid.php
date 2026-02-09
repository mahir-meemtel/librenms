<?php
namespace ObzoraNMS\Util;

use Cache;
use ObzoraNMS\Exceptions\InvalidIpException;
use ObzoraNMS\Exceptions\InvalidOidException;

class Oid
{
    public function __construct(
        public readonly string $oid
    ) {
    }

    public static function of(string $oid): static
    {
        return new static($oid);
    }

    /**
     * Convert a string to an oid encoded string
     */
    public static function encodeString(string $string): static
    {
        $oid = strlen($string);
        for ($i = 0; $i != strlen($string); $i++) {
            $oid .= '.' . ord($string[$i]);
        }

        return new static($oid);
    }

    public function isNumeric(): bool
    {
        return (bool) preg_match('/^[.\d]+$/', $this->oid);
    }

    public function isFullTextualOid(): bool
    {
        return (bool) preg_match('/[-_A-Za-z0-9]+::[-_A-Za-z0-9]+/', $this->oid);
    }

    public function hasMib(): bool
    {
        return str_contains($this->oid, '::');
    }

    /**
     * Get the MIB of this OID (if it has one)
     */
    public function getMib(): string
    {
        if ($this->hasMib()) {
            return explode('::', $this->oid, 2)[0];
        }

        return '';
    }

    public function hasNumericRoot(): bool
    {
        return (bool) preg_match('/^\.?1/', $this->oid);
    }

    public function isValid(string $oid): bool
    {
        return $this->isNumeric() || $this->isFullTextualOid();
    }

    public static function hasNumeric(array $oids): bool
    {
        foreach ($oids as $oid) {
            if (self::of($oid)->isNumeric()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert this OID to an IP
     *
     * @throws InvalidIpException
     */
    public function toIp(): IP
    {
        return IP::fromSnmpString($this->oid);
    }

    /**
     * Converts an oid to numeric and caches the result
     *
     * @throws InvalidOidException
     */
    public function toNumeric(?string $mib = 'ALL'): string
    {
        if ($this->isNumeric()) {
            return $this->oid;
        }

        // we already have a specific mib, don't add a bunch of others
        if ($this->hasMib()) {
            $mib = null;
        }

        $key = 'Oid:toNumeric:' . $this->oid . '/' . $mib;

        // only cache for this runtime
        $numeric_oid = Cache::driver('array')->remember($key, null, function () use ($mib) {
            $snmpQuery = \SnmpQuery::numeric();

            if ($mib) {
                $snmpQuery->mibs([$mib], append: $mib !== 'ALL'); // append to base mibs unless using ALL
            }

            return $snmpQuery->translate($this->oid);
        });

        if (empty($numeric_oid)) {
            throw new InvalidOidException("Unable to translate oid $this->oid");
        }

        return $numeric_oid;
    }

    public function __toString(): string
    {
        return $this->oid;
    }
}
