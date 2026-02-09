<?php
namespace App\Http\Controllers;

use App\Models\UserWidget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WidgetSettingsController extends Controller
{
    public function update(Request $request, UserWidget $widget): JsonResponse
    {
        $this->validate($request, [
            'settings' => 'array',
            'settings.refresh' => 'int|min:1',
        ]);

        $widget_settings = (array) $request->get('settings', []);
        unset($widget_settings['_token']);

        if (! $request->user()->can('update', $widget->dashboard)) {
            return response()->json([
                'status' => 'error',
                'message' => 'ERROR: You have no write-access to this dashboard',
            ]);
        }

        $widget->settings = $widget_settings;
        if ($widget->save()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Updated widget settings',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'ERROR: Could not update',
        ]);
    }
}
