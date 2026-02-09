<?php
namespace App\Http\Controllers\Ajax;

use App\Facades\ObzoraConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Process\Process;

class NetCommand extends Controller
{
    public function run(Request $request)
    {
        $this->validate($request, [
            'cmd' => 'in:whois,ping,tracert,nmap',
            'query' => 'ip_or_hostname',
        ]);

        ini_set('allow_url_fopen', '0');

        switch ($request->get('cmd')) {
            case 'whois':
                $cmd = [ObzoraConfig::get('whois', 'whois'), $request->get('query')];
                break;
            case 'ping':
                $cmd = [ObzoraConfig::get('ping', 'ping'), '-c', '5', $request->get('query')];
                break;
            case 'tracert':
                $cmd = [ObzoraConfig::get('mtr', 'mtr'), '-r', '-c', '5', $request->get('query')];
                break;
            case 'nmap':
                if (! $request->user()->isAdmin()) {
                    return response('Insufficient privileges');
                } else {
                    $cmd = [ObzoraConfig::get('nmap', 'nmap'), $request->get('query')];
                }
                break;
            default:
                return response('Invalid command');
        }

        $proc = new Process($cmd);
        $proc->setTimeout(240);

        //stream output
        return (new StreamedResponse(
            function () use ($proc, $request) {
                // a bit dirty, bust browser initial cache
                $ua = $request->header('User-Agent');
                if (Str::contains($ua, ['Chrome', 'Trident'])) {
                    $char = "\f"; // line feed
                } else {
                    $char = '';
                }
                echo str_repeat($char, 4096);
                echo PHP_EOL; // avoid first line mess ups due to line feed

                $proc->run(function ($type, $buffer) {
                    echo $buffer;
                    ob_flush();
                    flush();
                });
            },
            200,
            [
                'Content-Type' => 'text/plain; charset=utf-8',
                'X-Accel-Buffering' => 'no',
            ]
        ))->send();
    }
}
