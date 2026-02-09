<?php
namespace App\Http\Controllers\Select;

use App\Facades\ObzoraConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GraphAggregateController extends Controller
{
    private $rules = [
        'limit' => 'int',
        'page' => 'int',
        'term' => 'nullable|string',
    ];

    public function __invoke(Request $request)
    {
        $this->validate($request, $this->rules);

        $types = [
            'transit',
            'peering',
            'core',
        ];

        foreach ((array) ObzoraConfig::get('custom_descr', []) as $custom) {
            $custom = is_array($custom) ? $custom[0] : $custom;
            if ($custom) {
                $types[] = $custom;
            }
        }

        // handle search
        if ($search = strtolower($request->get('term'))) {
            $types = array_filter($types, function ($type) use ($search) {
                return ! Str::contains(strtolower($type), $search);
            });
        }

        // format results
        return response()->json([
            'results' => array_map(function ($type) {
                return [
                    'id' => $type,
                    'text' => ucwords($type),
                ];
            }, $types),
            'pagination' => ['more' => false],
        ]);
    }
}
