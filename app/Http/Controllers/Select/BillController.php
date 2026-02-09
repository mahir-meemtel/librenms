<?php
namespace App\Http\Controllers\Select;

use App\Models\Bill;

class BillController extends SelectController
{
    protected function searchFields($request)
    {
        return ['bill_name', 'bill_notes'];
    }

    /**
     * Defines the base query for this resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function baseQuery($request)
    {
        return Bill::hasAccess($request->user())
            ->select('bill_id', 'bill_name');
    }
}
