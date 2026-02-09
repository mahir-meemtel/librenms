<?php
namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AvailabilityMapController extends Controller
{
    public function setView(Request $request)
    {
        $this->validate($request, [
            'map_view' => 'required|numeric|in:0,1,2',
        ]);

        return $this->setSessionValue($request, 'map_view');
    }

    public function setGroup(Request $request)
    {
        $this->validate($request, [
            'group_view' => 'required|numeric',
        ]);

        return $this->setSessionValue($request, 'group_view');
    }

    /**
     * @param  Request  $request
     * @param  string  $key
     * @return \Illuminate\Http\JsonResponse
     */
    private function setSessionValue($request, $key)
    {
        $value = $request->get($key);
        $request->session()->put($key, $value);

        return response()->json([$key, $value]);
    }
}
