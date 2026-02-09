<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DynamicConfig
{
    private $definitions;

    public function __construct()
    {
        // prepare to mark overridden settings
        $config = [];
        @include base_path('config.php');

        $this->definitions = collect(ObzoraConfig::getDefinitions())->map(function ($item, $key) use ($config) {
            $item['overridden'] = Arr::has($config, $key);

            return new DynamicConfigItem($key, $item);
        });
    }

    /**
     * Check if a setting is valid
     *
     * @param  string  $name
     * @return bool
     */
    public function isValidSetting($name)
    {
        return $this->definitions->has($name) && $this->definitions->get($name)->isValid();
    }

    /**
     * Get config item by name
     *
     * @param  string  $name
     * @return DynamicConfigItem|null
     */
    public function get($name)
    {
        return $this->definitions->get($name);
    }

    /**
     * Get all groups defined
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->definitions->pluck('group')->unique()->filter()->prepend('global');
    }

    public function getSections()
    {
        /** @var Collection $sections */
        $sections = $this->definitions->groupBy('group')->map(function ($items) {
            return $items->pluck('section')->unique()->filter()->values();
        })->sortBy(function ($item, $key) {
            return $key;
        });
        $sections->prepend($sections->pull('', []), 'global'); // rename '' to global

        return $sections;
    }

    /**
     * Get all config definitions grouped by group then section.
     *
     * @return Collection
     */
    public function getGrouped()
    {
        /** @var Collection $grouped */
        $grouped = $this->definitions->filter->isValid()->sortBy('group')->groupBy('group')->map(function ($group) {
            return $group->sortBy('section')->groupBy('section')->map(function ($section) {
                /** @var Collection $section */
                return $section->sortBy('order')->pluck('name');
            });
        });
        $grouped->prepend($grouped->pull(''), 'global'); // rename '' to global

        return $grouped;
    }

    public function getByGroup($group, $subgroup = null)
    {
        return $this->definitions->filter(function ($item) use ($group, $subgroup) {
            /** @var DynamicConfigItem $item */
            if ($item->getGroup() != $group) {
                return false;
            }

            if ($subgroup && $item->getSection() != $subgroup) {
                return false;
            }

            return $item->isValid();
        })->sortBy('order');
    }

    /**
     * Get all config items keyed by name
     *
     * @return Collection
     */
    public function all()
    {
        return $this->definitions;
    }
}
