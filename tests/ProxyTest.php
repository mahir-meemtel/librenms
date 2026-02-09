<?php
namespace ObzoraNMS\Tests;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Util\Http;
use ObzoraNMS\Util\Version;

class ProxyTest extends TestCase
{
    public function testClientAgentIsCorrect(): void
    {
        $this->assertEquals('ObzoraNMS/' . Version::VERSION, Http::client()->getOptions()['headers']['User-Agent']);
    }

    public function testProxyIsNotSet(): void
    {
        ObzoraConfig::set('http_proxy', '');
        ObzoraConfig::set('https_proxy', '');
        ObzoraConfig::set('no_proxy', '');
        $client_options = Http::client()->getOptions();
        $this->assertEmpty($client_options['proxy']['http']);
        $this->assertEmpty($client_options['proxy']['https']);
        $this->assertEmpty($client_options['proxy']['no']);
    }

    public function testProxyIsSet(): void
    {
        ObzoraConfig::set('http_proxy', 'http://proxy:5000');
        ObzoraConfig::set('https_proxy', 'tcp://proxy:5183');
        ObzoraConfig::set('no_proxy', 'localhost,127.0.0.1,::1,.domain.com');
        $client_options = Http::client()->getOptions();
        $this->assertEquals('http://proxy:5000', $client_options['proxy']['http']);
        $this->assertEquals('tcp://proxy:5183', $client_options['proxy']['https']);
        $this->assertEquals([
            'localhost',
            '127.0.0.1',
            '::1',
            '.domain.com',
        ], $client_options['proxy']['no']);
    }

    public function testProxyIsSetFromEnv(): void
    {
        ObzoraConfig::set('http_proxy', '');
        ObzoraConfig::set('https_proxy', '');
        ObzoraConfig::set('no_proxy', '');

        putenv('HTTP_PROXY=someproxy:3182');
        putenv('HTTPS_PROXY=https://someproxy:3182');
        putenv('NO_PROXY=.there.com');

        $client_options = Http::client()->getOptions();
        $this->assertEquals('someproxy:3182', $client_options['proxy']['http']);
        $this->assertEquals('https://someproxy:3182', $client_options['proxy']['https']);
        $this->assertEquals([
            '.there.com',
        ], $client_options['proxy']['no']);

        putenv('http_proxy=otherproxy:3182');
        putenv('https_proxy=otherproxy:3183');
        putenv('no_proxy=dontproxymebro');

        $client_options = Http::client()->getOptions();
        $this->assertEquals('otherproxy:3182', $client_options['proxy']['http']);
        $this->assertEquals('otherproxy:3183', $client_options['proxy']['https']);
        $this->assertEquals([
            'dontproxymebro',
        ], $client_options['proxy']['no']);
    }
}
