<?php
namespace App\Http\Controllers;

use App\Models\Port;
use Illuminate\Support\Facades\Validator;

class PortController extends Controller
{
    public function update(\Illuminate\Http\Request $request, Port $port)
    {
        $validated = Validator::make($request->json()->all(), [
            'groups' => 'array',
            'groups.*' => 'int',
        ])->validate();

        $updated = false;
        $message = '';

        if (array_key_exists('groups', $validated)) {
            $changes = $port->groups()->sync($validated['groups']);
            $groups_updated = array_sum(array_map(function ($group_ids) {
                return count($group_ids);
            }, $changes));

            if ($groups_updated > 0) {
                $message .= trans('port.groups.updated', ['port' => $port->getLabel()]);
                $updated = true;
            }
        }

        return $updated
            ? response(['message' => $message])
            : response(['message' => trans('port.groups.none', ['port' => $port->getLabel()])], 400);
    }
}
