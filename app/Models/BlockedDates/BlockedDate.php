<?php

namespace App\Models\BlockedDates;

use App\Extenders\Models\BaseModel as Model;
use App\Models\Destinations\Destination;
use Illuminate\Support\Facades\Log;


class BlockedDate extends Model
{

	protected $dates = ['date'];
    protected $fillable = ['name', 'mode', 'destination_id','date'];

    public function dates() 
    {
        return $this->hasMany(Date::class);
    }

    public function destination() 
    {
        return $this->belongsTo(Destination::class)->withTrashed();
    }

    /**
     * @Setters
     */
    public static function store($request, $item = null, $columns = ['name', 'mode', 'description', 'destination_id'])
    {
        $vars = $request->only($columns);
        Log::info('request');
        Log::info($request);
        if (!$item) {
            Log::info('!item');
            Log::info($item);
            $item = static::create($vars);
        } else {
            Log::info('!item else');
            Log::info($item);
            $item->update($vars);
        }

        return $item;
    }

    /**
     * @Render
     */
    public function renderShowUrl($prefix = 'admin') {
        return route($prefix . '.blocked-dates.show', $this->id);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.blocked-dates.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.blocked-dates.restore', $this->id);
    }
}
