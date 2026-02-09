<?php
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class SimpleTableController extends Controller
{
    public static $base_rules = [
        'current' => 'int',
        'rowCount' => 'int',
        'searchPhrase' => 'nullable|string',
        'sort.*' => 'in:asc,desc',
    ];

    /**
     * Validate the given request with the given rules.
     *
     * @param  Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     */
    public function validate(Request $request, array $rules = [], array $messages = [], array $customAttributes = [])
    {
        $full_rules = array_replace(self::$base_rules, $rules);

        return parent::validate($request, $full_rules, $messages, $customAttributes);
    }

    /**
     * @param  array|Collection  $rows
     * @param  int  $page
     * @param  int  $currentCount
     * @param  int  $total
     * @return \Illuminate\Http\JsonResponse
     */
    protected function formatResponse($rows, $page, $currentCount, $total)
    {
        return response()->json([
            'current' => $page,
            'rowCount' => $currentCount,
            'rows' => $rows,
            'total' => $total,
        ]);
    }
}
