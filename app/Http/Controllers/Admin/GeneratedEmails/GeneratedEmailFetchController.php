<?php

namespace App\Http\Controllers\Admin\GeneratedEmails;

use App\Extenders\Controllers\FetchController;

use Illuminate\Http\Request;

use App\Models\Emails\GeneratedEmail;
use App\Models\Destinations\Destination;

class GeneratedEmailFetchController extends FetchController
{
    /**
     * Set object class of fetched data
     * 
     * @return void
     */
    public function setObjectClass()
    {
        $this->class = new GeneratedEmail;
    }

    /**
     * Custom filtering of query
     * 
     * @param Illuminate\Support\Facades\DB $query
     * @return Illuminate\Support\Facades\DB $query
     */
    public function filterQuery($query)
    {
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
            'email_to' => $item->email_to,
            'type' => $item->notification_type,
            'title' => $item->title,
            'experience_name' => $item->experience->name ?? '--',
            'message' => str_limit($item->message, 15),
            'created_at' => $item->renderDate(),
            'showUrl' => $item->renderShowUrl(),
            'archiveUrl' => $item->renderArchiveUrl(),
            'restoreUrl' => $item->renderRestoreUrl(),
            'deleted_at' => $item->deleted_at,
        ];
    }

    public function fetchView(Request $request) {
        $item = null;
        $existingTrailIds = [];
        $trails = [];
        $except = [
            'Masungi: Initial Payment Confirmation',
            'Masungi: Remaining Balance Confirmation',
            'Masungi: Full Payment Confirmation',
            'Masungi: Reservation Approved',
        ];
        $existed_types = GeneratedEmail::whereNotIn('notification_type', $except)->pluck('notification_type');
        $types = collect(GeneratedEmail::getTypes())->whereNotIn('value', $existed_types)->toArray();
        $email_types = collect(GeneratedEmail::getEmailTypes())->toArray();
        /* Masungi "trails" are labeled as "experiences/allocations" on the Visita back-end" */
        if($request->has('notification_type')) {
            /* Fetch the generated email of the selected notification type on the front-end that the experience_ids are not null, then pluck to an array */
            $existingTrailIds = GeneratedEmail::where('notification_type', $request->notification_type)
                ->whereNotNull('experience_id')
                ->pluck('experience_id')
                ->toArray();
            $trails = Destination::find(5)->allocations()->whereNotIn('id', $existingTrailIds)->get();
        } else {
            $trails = Destination::find(5)->allocations()->get();
        }

        if ($request->id) {
            /* The current trail ID & the other available IDs */
        	$item = GeneratedEmail::withTrashed()->findOrFail($request->id);
        	array_push($types, [ 'label' => $item->notification_type, 'value' => $item->notification_type ]);
            $item->archiveUrl = $item->renderArchiveUrl();
            $item->restoreUrl = $item->renderRestoreUrl();

            $existingTrailIds = GeneratedEmail::where('notification_type', $request->notification_type)
                ->whereHas('experience', function($query) use($item) {
                    $query->where('id', '!=', $item->experience_id);
                })
                ->whereNotNull('experience_id')
                ->pluck('experience_id')
                ->toArray();
            $trails = Destination::find(5)->allocations()->whereNotIn('id', $existingTrailIds)->get();
        }

    	return response()->json([
    		'item' => $item,
            'types' => $types,
            'email_types' => $email_types,
            'trails' => $trails,
    	]);
    }
}
