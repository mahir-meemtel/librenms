<?php
namespace App\Http\Controllers\Ajax;

use App\Facades\ObzoraConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class SearchController
{
    public function __invoke(Request $request): JsonResponse
    {
        $search = $request->get('search');
        if (empty($search)) {
            return new JsonResponse;
        }

        $query = $this->buildQuery($search, $request)
            ->limit((int) ObzoraConfig::get('webui.global_search_result_limit'));

        return response()->json($query->get()->map([$this, 'formatItem']));
    }

    abstract public function buildQuery(string $search, Request $request): Builder;

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $item
     * @return array
     */
    abstract public function formatItem($item): array;
}
