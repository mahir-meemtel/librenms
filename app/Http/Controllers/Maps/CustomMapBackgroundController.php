<?php
namespace App\Http\Controllers\Maps;

use App\Http\Controllers\Controller;
use App\Models\CustomMap;
use App\Models\CustomMapBackground;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class CustomMapBackgroundController extends Controller
{
    public function get(CustomMap $map)
    {
        $this->authorize('view', $map);

        if ($map->background_type !== 'image') {
            abort(404);
        }

        // explicitly use file cache
        try {
            $imageContent = Cache::driver('file')
                ->remember($this->getCacheKey($map), new \DateInterval('P30D'), fn () => $map->background->background_image);
        } catch (\ErrorException $e) {
            // if cache fails, just load from database :(
            $imageContent = $map->background->background_image;
        }

        if (empty($imageContent)) {
            abort(404);
        }

        return response($imageContent, headers: [
            'Content-Type' => $map->background_data['mime'] ?? getimagesizefromstring($imageContent)['mime'] ?? 'image/jpeg',
        ]);
    }

    public function save(FormRequest $request, CustomMap $map)
    {
        $this->authorize('update', $map);
        $this->validate($request, [
            'type' => 'in:image,color,map,none',
            'image' => 'required_if:type,image|mimes:png,jpg,svg,gif',
            'color' => 'required_if:type,color|regex:/^#[0-9a-f]{6,8}$/',
            'lat' => 'required_if:type,map|numeric|between:-90,90',
            'lng' => 'required_if:type,map|numeric|between:-180,180',
            'zoom' => 'required_if:type,map|integer|between:0,19',
            'layer' => 'string|regex:/^[a-zA-Z]*$/',
        ]);

        $map->background_type = $request->type;
        $this->updateBackgroundImage($map, $request);
        $map->background_data = array_merge($map->background_data ?? [], $request->only([
            'color',
            'lat',
            'lng',
            'zoom',
            'layer',
        ]));

        $map->save();

        return response()->json([
            'bgtype' => $map->background_type,
            'bgdata' => $map->getBackgroundConfig(),
        ]);
    }

    private function updateBackgroundImage(CustomMap $map, FormRequest $request): void
    {
        if ($map->background_type == 'image') {
            if ($request->image) {
                // if image type and we have image data (new image) save it
                $background = $map->background ?? new CustomMapBackground;

                $image_content = $request->image->getContent();
                $mimeType = $request->image->getMimeType();

                // sanitize SVGs
                if ($mimeType == 'image/svg+xml') {
                    $image_content = (new Sanitizer)->sanitize($image_content);
                }

                $background->background_image = $image_content;

                $map->background()->save($background);
                Cache::driver('file')->forget($this->getCacheKey($map)); // clear old image cache if present
                $map->background_data = array_merge($map->background_data ?? [], [
                    'version' => md5($background->background_image),
                    'original_filename' => $request->image->getClientOriginalName(),
                    'mime' => $mimeType,
                ]);
            }
        } elseif ($map->getOriginal('background_type') == 'image') {
            // if no longer image, clean up. if there are multiple web servers, it will only clear from the local.
            Cache::driver('file')->forget($this->getCacheKey($map));
            $map->background()->delete();
            // remove image keys from background data
            $map->background_data = array_diff_key($map->background_data ?? [], [
                'version' => 1,
                'original_filename' => 1,
                'mime' => 1,
            ]);
        }
    }

    private function getCacheKey(CustomMap $map): string
    {
        return 'custommap_background_' . $map->custom_map_id . ':' . ($map->background_data['version'] ?? '');
    }
}
