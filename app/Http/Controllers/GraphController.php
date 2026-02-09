<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ObzoraNMS\Enum\ImageFormat;
use ObzoraNMS\Exceptions\RrdGraphException;
use ObzoraNMS\Util\Debug;
use ObzoraNMS\Util\Graph;
use ObzoraNMS\Util\Url;

class GraphController extends Controller
{
    /**
     * @throws RrdGraphException
     */
    public function __invoke(Request $request, string $path = ''): Response
    {
        $vars = array_merge(Url::parseLegacyPathVars($request->path()), $request->except(['username', 'password']));

        if (\Auth::check()) {
            // only allow debug for logged in users
            Debug::set(! empty($vars['debug']));
        }

        try {
            $graph = Graph::get($vars);

            if (Debug::isEnabled()) {
                return response('<img src="' . $graph->inline() . '" alt="graph" />');
            }

            $headers = [
                'Content-type' => $graph->contentType(),
            ];

            if ($request->get('output') == 'base64') {
                return response($graph->base64(), 200, $headers);
            }

            return response($graph->data, 200, $headers);
        } catch (RrdGraphException $e) {
            if (Debug::isEnabled()) {
                throw $e;
            }

            return response($e->generateErrorImage(), 500, ['Content-type' => ImageFormat::forGraph($vars['graph_type'] ?? null)->contentType()]);
        }
    }
}
