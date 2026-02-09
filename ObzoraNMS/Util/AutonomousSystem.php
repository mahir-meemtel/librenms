<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use ErrorException;
use Illuminate\Support\Facades\Cache;

class AutonomousSystem
{
    public function __construct(
        private int $asn
    ) {
    }

    public static function get(int|string $asn): self
    {
        return app(AutonomousSystem::class, ['asn' => (int) $asn]);
    }

    /**
     * Get the ASN text from Team Cymru.
     * May be overridden in the config with astext.<asn>
     * Caches results for 1 day
     */
    public function name(): string
    {
        return Cache::remember("astext.$this->asn", 86400, function () {
            if (ObzoraConfig::has("astext.$this->asn")) {
                return ObzoraConfig::get("astext.$this->asn");
            }

            try {
                $result = @dns_get_record("AS$this->asn.asn.cymru.com", DNS_TXT);

                if (! empty($result[0]['txt'])) {
                    $txt = explode('|', $result[0]['txt']);

                    return trim($txt[4], ' "');
                }
            } catch (ErrorException $e) {
            }

            return '';
        });
    }
}
