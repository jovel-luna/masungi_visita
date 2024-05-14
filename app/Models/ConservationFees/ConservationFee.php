<?php

namespace App\Models\ConservationFees;

use App\Helpers\StringHelpers;

use App\Extenders\Models\BaseModel as Model;

use App\Models\Allocations\Allocation;
use App\Models\Fees\Fee;
use App\Models\Types\VisitorType;

class ConservationFee extends Model
{
    public function allocation()
    {
        return $this->belongsTo(Allocation::class, 'experience_id')->withTrashed();
    }

    public function specialFee()
    {
        return $this->belongsTo(Fee::class)->withTrashed();
    }

    public function visitorType()
    {
        return $this->belongsTo(VisitorType::class)->withTrashed();
    }

     /**
     * @Setters
     */
    public static function store($request, $item = null, $columns = ['name', 'experience_id'])
    {
        $vars = $request->only($columns);

        if (!$item) {
            $item = static::create($vars);
        } else {
            $vars['experience_id'] = $request['experience_id'];
            $vars['special_fee_id'] = $request['special_fee_id'];
            $vars['visitor_type_id'] = $request['visitor_type_id'];
            $vars['weekday_fee'] = $request['weekday_fee'];
            $vars['weekend_fee'] = $request['weekend_fee'];
            $item->update($vars);
        }

        return $item;
    }

    public static function getFormattedFees()
    {
        $items = [];
        $fees = static::get();
        foreach ($fees as $fee) {
            array_push($items, [
                'id' => $fee->id,
                'display_name' => $fee->name
            ]);
        }

        return $items;
    }

    public static function getFilteredFees($allocation_id = null)
    {
        $items = [];

        $fees = static::where('experience_id', $allocation_id)->get();
        
        foreach ($fees as $fee) {
            array_push($items, [
                'id' => $fee->id,
                'display_name' => $fee->name
            ]);
        }

        return $items;
    }

    /**
     * @Render
     */
    public function renderShowUrl($prefix = 'admin') {
        return route($prefix . '.conservation-fees.show', $this->id);
    }

    public function renderArchiveUrl($prefix = 'admin') {
        return route($prefix . '.conservation-fees.archive', $this->id);
    }

    public function renderRestoreUrl($prefix = 'admin') {
        return route($prefix . '.conservation-fees.restore', $this->id);
    }
}
