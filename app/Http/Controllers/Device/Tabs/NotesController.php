<?php
namespace App\Http\Controllers\Device\Tabs;

use App\Models\Device;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\UI\DeviceTab;

class NotesController implements DeviceTab
{
    use AuthorizesRequests;

    public function visible(Device $device): bool
    {
        return true;
    }

    public function slug(): string
    {
        return 'notes';
    }

    public function icon(): string
    {
        return 'fa-file-text-o';
    }

    public function name(): string
    {
        return __('Notes');
    }

    public function data(Device $device, Request $request): array
    {
        return [];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Device  $device
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Device $device)
    {
        $this->authorize('update-notes', $device);

        $device->notes = $request->input('note');
        $device->save();

        return back();
    }
}
