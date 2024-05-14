<?php

namespace App\Models\Agencies;

use App\Extenders\Models\BaseModel as Model;

use App\Models\Allocations\AgencyAllocation;

class Agency extends Model
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
            'email' => $this->email,
            'contact_person' => $this->contact_person,
            'contact_number' => $this->contact_number,
        ];
        
        return $searchable;
    }

    public function agencyAllocations()
    {
    	return $this->hasMany(AgencyAllocation::class);
    }

     /**
     * @Setters
     */
    public static function store($request, $item = null, $columns = ['code', 'email', 'contact_person', 'contact_number', 'name'])
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

        return route($prefix . '.agencies.show', $route);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.agencies.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.agencies.restore', $this->id);
    }

}
