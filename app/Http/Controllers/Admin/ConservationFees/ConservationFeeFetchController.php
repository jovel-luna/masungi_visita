<?php

namespace App\Http\Controllers\Admin\ConservationFees;

use App\Extenders\Controllers\FetchController;

use App\Models\ConservationFees\ConservationFee;

use App\Models\Experiences\Experience;
use App\Models\Destinations\Destination;
use App\Models\Types\VisitorType;
use App\Models\Fees\Fee;
use App\Models\Allocations\Allocation;


class ConservationFeeFetchController extends FetchController
{
    /**
     * Set object class of fetched data
     * 
     * @return void
     */
    public function setObjectClass()
    {
        $this->class = new ConservationFee;
    }

    /**
     * Custom filtering of query
     * 
     * @param Illuminate\Support\Facades\DB $query
     * @return Illuminate\Support\Facades\DB $query
     */
    public function filterQuery($query)
    {

        $admin = auth()->guard('admin')->user();

        $destination_id =  $admin->destination_id;
        
        if($admin->destination_id) {
            $query = $query->whereHas('allocation', function($query) use($destination_id)
            {
                $query->where('destination_id', $destination_id);

            });
        }
        return $query;
    }

    /**
     * Custom formatting of data
     * 
     * @param Illuminate\Support\Collection $items
     * @return array $result
     */
    public function formatData($items)
    {
        $result = [];

        foreach($items as $item) {
            $data = $this->formatItem($item);
            array_push($result, $data);
        }

        return $result;
    }

    /**
     * Build array data
     * 
     * @param  App\Contracts\AvailablePosition
     * @return array
     */
    protected function formatItem($item)
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'destination' => $item->allocation->destination->name,
            'experience' => $item->allocation ? $item->allocation->name : '---',
            'created_at' => $item->renderDate(),
            'showUrl' => $item->renderShowUrl(),
            'archiveUrl' => $item->renderArchiveUrl(),
            'restoreUrl' => $item->renderRestoreUrl(),
            'deleted_at' => $item->deleted_at,
        ];
    }

    public function fetchView($id = null) {

        $item = null;

        $admin = auth()->guard('admin')->user();

        if($admin->destination_id) {
            $experiences = Destination::find($admin->destination_id)->allocations;
        } else {
            $experiences = Allocation::get();
        }
        

        $visitor_types = VisitorType::orderBy('name')->get();
        $special_fees = Fee::orderBy('name')->get();

        if ($id) {
        	$item = ConservationFee::withTrashed()->findOrFail($id);
            $item->experience = $item->experience_id;
            $item->visitor_type = $item->visitor_type_id;
            $item->special_fee = $item->special_fee_id;
            $item->weekday_fee = $item->weekday_fee;
            $item->weekend_fee = $item->weekend_fee;
            $item->archiveUrl = $item->renderArchiveUrl();
            $item->restoreUrl = $item->renderRestoreUrl();
        }

    	return response()->json([
    		'item' => $item,
            'experiences' => $experiences,
            'visitor_types' => $visitor_types,
            'special_fees' => $special_fees,
    	]);
    }
}
