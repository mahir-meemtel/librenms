<?php
namespace App\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController
{
    public function style(Request $request): JsonResponse
    {
        $request->validate([
            'style' => 'required|string|in:dark,light',
        ]);

        $request->session()->put('applied_site_style', $request->style);

        return response()->json(['style' => $request->style]);
    }

    public function resolution(Request $request): string
    {
        $request->validate([
            'width' => 'required|numeric',
            'height' => 'required|numeric',
        ]);

        // laravel session
        session([
            'screen_width' => $request->width,
            'screen_height' => $request->height,
        ]);

        return $request->width . 'x' . $request->height;
    }
}
