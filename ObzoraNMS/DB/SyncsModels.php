<?php
namespace ObzoraNMS\DB;

use App\Models\Device;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use ObzoraNMS\Interfaces\Models\Keyable;

trait SyncsModels
{
    /**
     * Sync several models for a device's relationship
     * Model must implement \ObzoraNMS\Interfaces\Models\Keyable interface
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parentModel
     * @param  string  $relationship
     * @param  \Illuminate\Support\Collection<Keyable>  $models  \ObzoraNMS\Interfaces\Models\Keyable
     * @return Collection
     */
    protected function syncModels($parentModel, $relationship, $models, $existing = null): Collection
    {
        $models = $models->keyBy->getCompositeKey();
        $existing = ($existing ?? $parentModel->$relationship)->groupBy->getCompositeKey();

        foreach ($existing as $exist_key => $existing_rows) {
            if ($models->offsetExists($exist_key)) {
                // update
                foreach ($existing_rows as $index => $existing_row) {
                    if ($index == 0) {
                        // fill attributes, ignoring mutators and fillable
                        $merged = array_merge($existing_row->getAttributes(), $models->get($exist_key)->getAttributes());
                        $existing_row->setRawAttributes($merged);
                        $existing_row->save();
                    } else {
                        // delete extra rows at this key
                        $existing_row->delete();
                        $existing_rows->forget($index);
                    }
                }
            } else {
                // delete
                $existing_rows->each->delete();
                $existing->forget($exist_key);
            }
        }

        $new = $models->diffKeys($existing);
        if (is_a($parentModel->$relationship(), HasManyThrough::class)) {
            // if this is a distant relation, the models need the intermediate relationship set
            // just save assuming things are correct
            $new->each->save();
        } else {
            $parentModel->$relationship()->saveMany($new);
        }

        return $existing->map->first()->merge($new);
    }

    /**
     * Sync a sub-group of models to the database
     *
     * @param  Collection<Keyable>  $models
     */
    public function syncModelsByGroup(Device $device, string $relationship, Collection $models, array $where): Collection
    {
        $filter = function ($models, $params) {
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    $models = $models->where(...$value);
                } else {
                    $models = $models->where($key, '=', $value);
                }
            }

            return $models;
        };

        return $this->syncModels($device, $relationship, $models->when($where, $filter), $device->$relationship->when($where, $filter));
    }

    /**
     * Combine a list of existing and potentially new models
     * If the model exists fill any new data from the new models
     *
     * @param  Collection  $existing  \ObzoraNMS\Interfaces\Models\Keyable
     * @param  Collection  $discovered  \ObzoraNMS\Interfaces\Models\Keyable
     * @return Collection
     */
    protected function fillNew(Collection $existing, Collection $discovered): Collection
    {
        $all = $existing->keyBy->getCompositeKey();
        foreach ($discovered as $new) {
            if ($found = $all->get($new->getCompositeKey())) {
                $found->fill($new->getAttributes());
            } else {
                $all->put($new->getCompositeKey(), $new);
            }
        }

        return $all;
    }
}
