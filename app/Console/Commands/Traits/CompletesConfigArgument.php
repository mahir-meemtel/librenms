<?php
namespace App\Console\Commands\Traits;

use App\Console\Commands\InternalHttpRequest;
use App\Models\User;
use Illuminate\Support\Str;
use ObzoraNMS\Util\DynamicConfig;
use ObzoraNMS\Util\DynamicConfigItem;

trait CompletesConfigArgument
{
    public function completeArgument($name, $value, $previous)
    {
        if ($name == 'setting') {
            return (new DynamicConfig())->all()->keys()->filter(function ($setting) use ($value) {
                return Str::startsWith($setting, $value);
            })->toArray();
        } elseif ($name == 'value') {
            $config = (new DynamicConfig())->get($previous);

            switch ($config->getType()) {
                case 'select-dynamic':
                    return $this->suggestionsForSelectDynamic($config, $value);
                case 'select':
                    return $this->suggestionsForSelect($config, $value);
            }
        }

        return false;
    }

    protected function suggestionsForSelect(DynamicConfigItem $config, ?string $value): array
    {
        $options = new \Illuminate\Support\Collection($config['options']);
        $keyStartsWith = $options->filter(function ($description, $key) use ($value) {
            return Str::startsWith($key, $value);
        });

        // try to see if it matches a value (aka key)
        if ($keyStartsWith->isNotEmpty()) {
            return $keyStartsWith->keys()->all();
        }

        // last chance to try to find by the description
        return $options->filter(function ($description, $key) use ($value) {
            return Str::contains($description, $value);
        })->keys()->all();
    }

    protected function suggestionsForSelectDynamic(DynamicConfigItem $config, ?string $value): array
    {
        // need auth to make http request
        if ($admin = User::adminOnly()->first()) {
            $target = $config['options']['target'];
            $data = ['limit' => 10];
            if ($value) {
                $data['term'] = $value; // filter in sql
            }

            // make "http" request
            $results = (new InternalHttpRequest())
                ->actingAs($admin)
                ->json('GET', route("ajax.select.$target"), $data)->json('results');

            return array_column($results, 'id');
        }

        return [];
    }
}
