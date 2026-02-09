<?php
namespace ObzoraNMS\Util;

class Categorizer
{
    protected $items;
    protected $categorized = [];
    protected $categories = [];
    protected $skippable;

    public function __construct($items = [])
    {
        $this->skippable = function ($item) {
            return false;
        };
        $this->items = $items;
    }

    public function addCategory(string $category, callable $function)
    {
        $this->categories[$category] = $function;
        $this->categorized[$category] = [];
    }

    public function setSkippable(callable $function)
    {
        $this->skippable = $function;
    }

    public function categorize()
    {
        foreach ($this->items as $item) {
            foreach ($this->categories as $category => $test) {
                if (call_user_func($this->skippable, $item)) {
                    continue;
                }

                $result = call_user_func($test, $item);
                if ($result !== false) {
                    $this->categorized[$category][] = $result;
                }
            }
        }

        return $this->categorized;
    }
}
