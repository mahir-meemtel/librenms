<?php
namespace App\Http\Controllers\Ajax;

use App\ApiClients\RipeApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ObzoraNMS\Exceptions\ApiClientException;

class RipeNccApiController extends Controller
{
    public function raw(Request $request, RipeApi $api)
    {
        $this->validate($request, [
            'data_param' => 'required|in:whois,abuse-contact-finder',
            'query_param' => 'required|ip_or_hostname',
        ]);

        $is_whois = $request->get('data_param') == 'whois';

        try {
            $resource = $request->get('query_param');
            $output = $is_whois ? $api->getWhois($resource) : $api->getAbuseContact($resource);

            return response()->json([
                'status' => 'ok',
                'message' => 'Queried',
                'output' => $output,
            ]);
        } catch (ApiClientException $e) {
            $response = $e->getOutput();
            $message = $e->getMessage();

            if (isset($response['messages'])) {
                $message .= ': ' . collect($response['messages'])
                        ->flatten()
                        ->reject(function ($value, $key) {
                            return $value != 'error';
                        })
                        ->implode(', ');
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'output' => $response,
            ], 503);
        }
    }
}
