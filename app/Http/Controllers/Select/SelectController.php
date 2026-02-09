<?php
namespace App\Http\Controllers\Select;

use App\Http\Controllers\PaginatedAjaxController;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class SelectController extends PaginatedAjaxController
{
    protected ?string $idField = null;
    protected ?string $textField = null;

    final protected function baseRules()
    {
        return [
            'limit' => 'int',
            'page' => 'int',
            'term' => 'nullable|string',
        ];
    }

    /**
     * The default method called by the route handler
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, $this->rules());
        $limit = $request->get('limit', 50);

        $query = $this->baseQuery($request);
        if ($this->idField && $this->textField) {
            $query->select([$this->idField, $this->textField]);
        }
        $this->filterById($query, $request->get('id'));
        $this->filter($request, $query, $this->filterFields($request));
        $this->search($request->get('term'), $query, $this->searchFields($request));
        $this->sort($request, $query);
        $paginator = $query->simplePaginate($limit);

        return $this->formatResponse($paginator);
    }

    /**
     * @param  Paginator|Collection  $paginator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function formatResponse($paginator)
    {
        $results = collect($paginator->items())->map([$this, 'formatItem']);

        // prepend the initial item, unless filtered out
        if ($this->canPrependFirstItem(request())) {
            $results->prepend($this->prependItem());
        }

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    /**
     * Default item formatting, should supply at least id and text keys
     * Check select2 docs.
     * Default implementation uses primary key and the first value in the model
     * If only one value is in the model attributes, that is the id and text.
     *
     * @param  Model  $model
     * @return array
     */
    public function formatItem($model)
    {
        if ($this->idField && $this->textField) {
            return [
                'id' => $model->getAttribute($this->idField),
                'text' => $model->getAttribute($this->textField),
            ];
        }

        // guess
        $attributes = collect($model->getAttributes());

        return [
            'id' => $attributes->count() == 1 ? $attributes->first() : $model->getKey(),
            'text' => $attributes->forget($model->getKeyName())->first(),
        ];
    }

    protected function prependItem(): ?array
    {
        return null;
    }

    protected function canPrependFirstItem(Request $request): bool
    {
        $item = $this->prependItem();

        if (empty($item)) {
            return false;
        }

        if ($request->page > 1) {
            return false;
        }

        if ($request->has('id') && $request->id != $item['id']) { // purposely loose comparison
            return false;
        }

        if ($request->has('term') && ! str_contains(strtolower($item['text']), strtolower($request->term))) {
            return false;
        }

        return true;
    }

    protected function filterById(EloquentBuilder|Builder $query, ?string $id): EloquentBuilder|Builder
    {
        if ($id) {
            // multiple
            if (str_contains($id, ',')) {
                $keys = explode(',', $id);

                return $this->idField ? $query->whereIn($this->idField, $keys) : $query->whereKey($keys);
            }

            // use id field if given
            return $this->idField ? $query->where($this->idField, $id) : $query->whereKey($id);
        }

        return $query;
    }
}
