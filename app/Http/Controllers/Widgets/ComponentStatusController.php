<?php
namespace App\Http\Controllers\Widgets;

use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComponentStatusController extends WidgetController
{
    protected string $name = 'component-status';
    protected $defaults = [
        'device_group' => null,
    ];

    /**
     * @param  Request  $request
     * @return View
     */
    public function getView(Request $request): string|View
    {
        $data = $this->getSettings();
        $status = [
            [
                'color' => 'text-success',
                'text' => __('Ok'),
            ],
            [
                'color' => 'grey',
                'text' => __('Warning'),
            ],
            [
                'color' => 'text-danger',
                'text' => __('Critical'),
            ],
        ];

        $component_status = Component::query()
            ->select('status', DB::raw("count('status') as total"))
            ->groupBy('status')
            ->where('disabled', '!=', 0)
            ->when($data['device_group'], function ($query) use ($data) {
                return $query->inDeviceGroup($data['device_group']);
            })
            ->get()->pluck('total', 'status')->toArray();

        foreach ($status as $key => $value) {
            $status[$key]['total'] = isset($component_status[$key]) ? $component_status[$key] : 0;
        }

        return view('widgets.component-status', ['status' => $status]);
    }
}
