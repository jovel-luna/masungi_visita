<?php

namespace App\Models\AgeRanges;

use App\Helpers\StringHelpers;

use App\Extenders\Models\BaseModel as Model;

class AgeRange extends Model
{
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
        return route($prefix . '.age-ranges.show', $this->id);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.age-ranges.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.age-ranges.restore', $this->id);
    }
}
