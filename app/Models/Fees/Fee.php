<?php

namespace App\Models\Fees;

use App\Extenders\Models\BaseModel as Model;

use App\Models\Allocations\Allocation;

class Fee extends Model
{
    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = [
            'id' => $this->id,
            'name' => $this->name,
        ];
        
        return $searchable;
    }

    // public function allocation()
    // {
    // 	return $this->belongsTo(Allocation::class)->withTrashed();
    // }

    /**
     * @Setters
     */
    public static function store($request, $item = null, $columns = ['name', 'color'])
    {
        $vars = $request->only($columns);

        if (!$item) {
            $item = static::create($vars);
        } else {
            $item->update($vars);
        }

        return $item;
    }

    /**
     * @Render
     */
    public function renderShowUrl($prefix = 'admin') {
        $route = $this->id;

        if ($prefix == 'web') {
            $route = [$this->id, $this->slug];
        }

        return route($prefix . '.fees.show', $route);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.fees.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.fees.restore', $this->id);
    }


    // public function renderAllocation() {
    //     return $this->allocation->name;
    // }

}
