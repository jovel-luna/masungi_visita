<?php

namespace App\Models\Nationalities;

use App\Helpers\StringHelpers;

use App\Extenders\Models\BaseModel as Model;

class Nationality extends Model
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
        return route($prefix . '.nationalities.show', $this->id);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.nationalities.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.nationalities.restore', $this->id);
    }
}
