<?php

namespace App\Http\Controllers\Admin\Capacities;

use Illuminate\Http\Request;

use App\Extenders\Controllers\FetchController;

use App\Models\Capacities\Capacity;
use App\Models\Allocations\Allocation;

class CapacityFetchController extends FetchController
{
    /**
     * Set object class of fetched data
     * 
     * @return void
     */
    public function setObjectClass()
    {
        $this->class = new Capacity;
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
            $experiences = $admin->destination->allocations->pluck('id');
            $query = $query->whereIn('allocation_id', $experiences);
        }

        if($this->request->filled('destination_show')) {
            $destination = $this->request->destination_show;
            $query = $query->whereHas('allocation', function($query) use($destination) {
                $query->where('destination_id', $destination);
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

        $admin = auth()->guard('admin')->user();

        foreach($items as $item) {

            if($admin->getRoleNames()[0] === 'Destination Manager') {
                if($item->allocation->destination_id === $admin->destination_id) {
                    array_push($result, [
                        'id' => $item->id,
                        'allocation' => $item->allocation->name,
                        'online' => $item->online,
                        'mgt_lgu' => $item->mgt_lgu,
                        'walk_in' => $item->walk_in,
                        'agency' => $item->agency,
                        'total' => $item->agency + $item->walk_in + $item->mgt_lgu + $item->online,
                        'created_at' => $item->renderDate(),
                        'showUrl' => $item->renderShowUrl(),
                        'archiveUrl' => $item->renderArchiveUrl(),
                        'restoreUrl' => $item->renderRestoreUrl(),
                        'deleted_at' => $item->deleted_at,
                    ]);
                }
            } else {
                $data = $this->formatItem($item);
                array_push($result, $data);
            }
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
            'allocation' => $item->allocation->name,
            'online' => $item->online,
            'mgt_lgu' => $item->mgt_lgu,
            'walk_in' => $item->walk_in,
            'agency' => $item->agency,
            'total' => $item->agency + $item->walk_in + $item->mgt_lgu + $item->online,
            'created_at' => $item->renderDate(),
            'showUrl' => $item->renderShowUrl(),
            'archiveUrl' => $item->renderArchiveUrl(),
            'restoreUrl' => $item->renderRestoreUrl(),
            'deleted_at' => $item->deleted_at,
        ];
    }

    public function fetchView(Request $request, $id = null) {
        $item = null;
        $ids = collect(Capacity::all())->pluck('allocation_id');
        $admin = auth()->guard('admin')->user();
        $allocations = Allocation::with('destination')->whereNotIn('id', $ids)->get();

        $filtered_capacity_online = 0;
        $filtered_capacity_walk_in = 0;
        $filtered_capacity_mgt_lgu = 0;
        $filtered_capacity_agency = 0;
        $filtered_total_capacity = 0;

        $capacity_online = 0;
        $capacity_walk_in = 0;
        $capacity_mgt_lgu = 0;
        $capacity_agency = 0;
        $total_capacity = 0;

        if($admin->destination_id) {
            $allocations = Allocation::where('destination_id', $admin->destination_id)->with('destination')->whereNotIn('id', $ids)->get();
        }


        if(!$id && $request->filled('id')) 
        {
            $all_filtered_allocations  = Allocation::where('destination_id', $request->id)->get();
            foreach($all_filtered_allocations as $all_filtered_allocation) {
                $capacity_online += $all_filtered_allocation->capacities->sum('online');
                $capacity_walk_in += $all_filtered_allocation->capacities->sum('walk_in');
                $capacity_mgt_lgu += $all_filtered_allocation->capacities->sum('mgt_lgu');
                $capacity_agency += $all_filtered_allocation->capacities->sum('agency');

                $total_capacity = $capacity_online + $capacity_walk_in + $capacity_mgt_lgu + $capacity_agency;
            }
        } 

        if ($id) {
        	$item = Capacity::withTrashed()->findOrFail($id);
        	$allocations = Allocation::with('destination')->whereNotIn('id', $ids)->orWhere('id', $item->allocation_id)->get();
            $item->archiveUrl = $item->renderArchiveUrl();
            $item->restoreUrl = $item->renderRestoreUrl();

            $filtered_allocations  = Allocation::where('destination_id', $item->allocation->destination_id)->where('id', '!=', $item->allocation_id)->get();
            foreach($filtered_allocations as $filtered_allocation) {
                $filtered_capacity_online += $filtered_allocation->capacities->sum('online');
                $filtered_capacity_walk_in += $filtered_allocation->capacities->sum('walk_in');
                $filtered_capacity_mgt_lgu += $filtered_allocation->capacities->sum('mgt_lgu');
                $filtered_capacity_agency += $filtered_allocation->capacities->sum('agency');

                $filtered_total_capacity = $filtered_capacity_online + $filtered_capacity_walk_in + $filtered_capacity_mgt_lgu + $filtered_capacity_agency;
            }

            $all_filtered_allocations  = Allocation::where('destination_id', $item->allocation->destination_id)->get();
            foreach($all_filtered_allocations as $all_filtered_allocation) {
                $capacity_online += $all_filtered_allocation->capacities->sum('online');
                $capacity_walk_in += $all_filtered_allocation->capacities->sum('walk_in');
                $capacity_mgt_lgu += $all_filtered_allocation->capacities->sum('mgt_lgu');
                $capacity_agency += $all_filtered_allocation->capacities->sum('agency');

                $total_capacity = $capacity_online + $capacity_walk_in + $capacity_mgt_lgu + $capacity_agency;
            }
        }


    	return response()->json([
    		'item' => $item,
    		'allocations' => $allocations,
            'total_capacity' => $total_capacity,
            'filtered_total_capacity' => $filtered_total_capacity,
    	]);
    }
}
