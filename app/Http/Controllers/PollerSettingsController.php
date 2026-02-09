<?php
namespace App\Http\Controllers;

use App\Models\PollerCluster;
use Illuminate\Http\Request;

class PollerSettingsController extends Controller
{
    public function update(Request $request, $id, $setting)
    {
        $poller = PollerCluster::findOrFail($id);
        $this->authorize('update', $poller);

        $definition = collect($poller->configDefinition())->keyBy('name');
        if (! $definition->has($setting)) {
            return response()->json(['error' => 'Invalid setting'], 422);
        }

        $poller->$setting = $request->get('value');
        $poller->save();

        return response()->json(['value' => $poller->$setting]);
    }

    public function destroy($id, $setting)
    {
        $poller = PollerCluster::findOrFail($id);
        $this->authorize('delete', $poller);

        $definition = collect($poller->configDefinition())->keyBy('name');
        if (! $definition->has($setting)) {
            return response()->json(['error' => 'Invalid setting'], 422);
        }

        $poller->$setting = $definition->get($setting)['default'];
        $poller->save();

        return response()->json(['value' => $poller->$setting]);
    }
}
