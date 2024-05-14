<?php

namespace App\Http\Controllers\Admin\Calendars;

use App\Extenders\Controllers\FetchController;

use App\Models\Books\Book;

class CalendarFetchController extends FetchController
{
    /**
     * Set object class of fetched data
     *
     * @return void
     */
    public function setObjectClass()
    {
        $this->class = new Book;
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
        $i = 0;
        $bookings = Book::where('destination_id', $this->request->destination_id)->where('allocation_id', $this->request->allocation_id)->orderBy('scheduled_at', 'asc')->with('destination')->get();

        $res = $bookings->groupBy(function ($result, $key) {
            return $result->scheduled_at->format('Y-m-d');
        })->map(function ($result) {
            return ($result);
        });

        foreach($res as $key => $item) {
            foreach($res[$key] as $parsedKey => $parsedData) {
                if(!collect($result)->contains('start', $key)) {
                    // dd($parsedData->allocation_id . ' - ' . $key);
                    // dd($parsedData->where('allocation_id', 1)->whereDate('scheduled_at', '2020-06-01'));
                    $booking = $parsedData->where('allocation_id', $parsedData->allocation_id)
                        ->whereDate('scheduled_at', $key)
                            ->whereHas('invoice', function($query) {
                            return $query->where('is_approved', true);
                        });
                    $guest_count = $booking->sum('total_guest');
                    array_push($result, [
                        'start' => $key,
                        'title' => $guest_count.'/'.$parsedData->destination->capacity_per_day,
                        'color' => 'transparent',
                        'textColor' => 'black',
                        'fontSize' => '2em'
                    ]);
                }
            }
        }

   //      $bookingGroup = $bookings->groupBy(function ($result, $key) {
   //          return $result->scheduled_at->format('Y-m-d');
   //      });

   //      foreach($bookingGroup as $date => $bookings) {
			// if(! collect($result)->contains('start', $date)) {
   //              if (isset($bookings[0])) {
   //                  // dd($parsedData->allocation_id . ' - ' . $date);
   //                  // dd($parsedData->where('allocation_id', 1)->whereDate('scheduled_at', '2020-06-01'));
   //                  $bookings = $bookings[0]->whereHas('invoice', function($query) {
   //                      return $query->where('is_approved', true);
   //                  })->whereDate('scheduled_at', $date);
   //                  $guest_count = $bookings->sum('total_guest');

   //  				array_push($result, [
   //  			        'start' => $date,
   //  			        'title' => $guest_count.'/'.$bookings->first()->destination->capacity_per_day,
   //  			        'color' => 'transparent',
   //  			        'textColor' => 'black',
   //  			        'fontSize' => '2em'
   //  			    ]);
   //              }
			// }
   //      }

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
            'events' => $item,
        ];
    }
}
