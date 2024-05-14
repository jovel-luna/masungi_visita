<?php

namespace App\Http\Controllers\Admin\Nationalities;

use App\Http\Requests\Admin\Nationalities\NationalityStoreRequest;

use App\Http\Controllers\Controller;

use App\Models\Nationalities\Nationality;

class NationalityController extends Controller
{

    public function __construct() {
        $this->middleware('App\Http\Middleware\Admin\Nationalities\NationalityMiddleware', 
            ['only' => ['index', 'create', 'store', 'show', 'update', 'archive', 'restore']]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.nationalities.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Nationality::withTrashed()->findOrFail($id);
        return view('admin.nationalities.show', [
            'item' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NationalityStoreRequest $request, $id)
    {
        $item = Nationality::withTrashed()->findOrFail($id);
        $message = "You have successfully updated {$item->name}";

        $item = Nationality::store($request, $item);

        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Nationality  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = Nationality::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->name}",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Nationality  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = Nationality::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->name}",
        ]);
    }
}
