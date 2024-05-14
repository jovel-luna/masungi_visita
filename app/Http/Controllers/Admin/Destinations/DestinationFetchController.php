<?php

namespace App\Http\Controllers\Admin\Destinations;

use App\Extenders\Controllers\FetchController;

use App\Models\Destinations\Destination;
use App\Models\Allocations\Allocation;
use App\Models\AddOns\AddOn;

use Carbon\Carbon;

class DestinationFetchController extends FetchController
{
    /**
     * Set object class of fetched data
     * 
     * @return void
     */
    public function setObjectClass()
    {
        $this->class = new Destination;
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
        
        if($admin->destination_id) {
            $query = $query->where('id', $admin->destination_id);
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
            'code' => $item->code,
            'icon' => $item->icon,
            'operating_hours' => Carbon::parse($item->operating_hours)->format('h:i:s A').'-'.Carbon::parse($item->operating_hours_end)->format('h:i:s A'),
            'capacity_per_day' => $item->capacity_per_day,
            'created_at' => $item->renderDate(),
            'showUrl' => $item->renderShowUrl(),
            'archiveUrl' => $item->renderArchiveUrl(),
            'restoreUrl' => $item->renderRestoreUrl(),
            'viewDestinationUrl' => $item->renderViewDestinationUrl(),
            'deleted_at' => $item->deleted_at,
        ];
    }

    public function fetchView($id = null) {
        $item = null;
        $images = null;
        $total_capacity = 0;

        if ($id) {
        	$item = Destination::withTrashed()->findOrFail($id);
	        $item->removeImageUrl = $item->renderRemoveImageUrl();
        	$item->name = $item->name;
            $item->add_ons = $item->addOns()->allRelatedIds();
        	$images = $item->getImages();
            $item->archiveUrl = $item->renderArchiveUrl();
            $item->restoreUrl = $item->renderRestoreUrl();

            $filtered_allocations  = Allocation::where('destination_id', $id)->get();
            $capacity_online = 0;
            $capacity_walk_in = 0;
            $capacity_mgt_lgu = 0;
            $capacity_agency = 0;
            $total_capacity = 0;
            foreach($filtered_allocations as $filtered_allocation) {
                $capacity_online += $filtered_allocation->capacities->sum('online');
                $capacity_walk_in += $filtered_allocation->capacities->sum('walk_in');
                $capacity_mgt_lgu += $filtered_allocation->capacities->sum('mgt_lgu');
                $capacity_agency += $filtered_allocation->capacities->sum('agency');

                $total_capacity = $capacity_online + $capacity_walk_in + $capacity_mgt_lgu + $capacity_agency;
            }
        }

        $add_ons = AddOn::all();

    	return response()->json([
    		'item' => $item,
    		'images' => $images,
            'add_ons' => $add_ons,
            'total_capacity' => $total_capacity
    	]);
    }
}
