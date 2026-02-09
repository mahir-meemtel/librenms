<?php
namespace App\ApiClients;

use App\Facades\ObzoraConfig;

class Oxidized extends BaseApi
{
    private bool $enabled;

    public function __construct()
    {
        $this->timeout = 90;
        $this->base_uri = ObzoraConfig::get('oxidized.url') ?? '';
        $this->enabled = ObzoraConfig::get('oxidized.enabled') === true && $this->base_uri;
    }

    /**
     * Ask oxidized to refresh the node list for the source (likely the ObzoraNMS API).
     */
    public function reloadNodes(): void
    {
        if ($this->enabled && ObzoraConfig::get('oxidized.reload_nodes') === true) {
            $this->getClient()->get('/reload.json');
        }
    }

    /**
     * Queues a hostname to be refreshed by Oxidized
     */
    public function updateNode(string $hostname, string $msg, string $username = 'not_provided'): bool
    {
        if ($this->enabled) {
            // Work around https://github.com/rack/rack/issues/337
            $msg = str_replace('%', '', $msg);

            return $this->getClient()
                ->put("/node/next/$hostname", ['user' => $username, 'msg' => $msg])
                ->successful();
        }

        return false;
    }

    /* Get content of the page */
    public function getContent(string $uri): string
    {
        if ($this->enabled) {
            return $this->getClient()->get($uri);
        } else {
            return '';
        }
    }
}
