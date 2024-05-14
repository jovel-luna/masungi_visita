<?php

namespace App\Models\Copywritings;

use App\Helpers\StringHelpers;

use App\Extenders\Models\BaseModel as Model;

class Copywriting extends Model
{
     /**
     * @Setters
     */
    public static function store($request, $item = null, $columns = ['name', 'description'])
    {
        $vars = $request->only($columns);

        $item->update($vars);

        return $item;
    }

    /**
     * @Render
     */
    public function renderShowUrl($prefix = 'admin') {
        return route($prefix . '.copywritings.show', $this->id);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.copywritings.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.copywritings.restore', $this->id);
    }
}
