<?php

namespace App\Http\Controllers\Admin\Guests;

use App\Http\Controllers\Controller;
use App\Models\Guests\Guest;

class GuestController extends Controller
{
    /**
     * Archive the specified resource from storage.
     *
     * @param  \App\Guest  $guests
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = Guest::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived a guest",
        ]);
    }
}
