<?php
namespace App\Http\Controllers\Select;

use App\ApiClients\GraylogApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;

class GraylogStreamsController extends Controller
{
    /**
     * The default method called by the route handler
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, GraylogApi $api)
    {
        $this->validate($request, [
            'limit' => 'int',
            'page' => 'int',
            'term' => 'nullable|string',
        ]);
        $search = strtolower($request->get('term'));

        $streams = [];
        try {
            $streams = collect($api->getStreams()['streams'])->filter(function ($stream) use ($search) {
                return ! $search || Str::contains(strtolower($stream['title']), $search) || Str::contains(strtolower($stream['description']), $search);
            })->map(function ($stream) {
                $text = $stream['title'];
                if ($stream['description']) {
                    $text .= " ({$stream['description']})";
                }

                return ['id' => $stream['id'], 'text' => $text];
            });
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        }

        return response()->json([
            'results' => $streams,
            'pagination' => ['more' => false],
        ]);
    }
}
