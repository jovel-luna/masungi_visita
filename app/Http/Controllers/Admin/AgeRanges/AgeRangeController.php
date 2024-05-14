<?php

namespace App\Http\Controllers\Admin\AgeRanges;

use App\Http\Requests\Admin\AgeRanges\AgeRangeStoreRequest;

use App\Http\Controllers\Controller;

use App\Models\AgeRanges\AgeRange;

class AgeRangeController extends Controller
{

    public function __construct() {
        $this->middleware('App\Http\Middleware\Admin\AgeRanges\AgeRangeMiddleware', 
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
        return view('admin.age-ranges.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = AgeRange::withTrashed()->findOrFail($id);
        return view('admin.age-ranges.show', [
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
    public function update(AgeRangeStoreRequest $request, $id)
    {
        $item = AgeRange::withTrashed()->findOrFail($id);
        $message = "You have successfully updated {$item->name}";

        $item = AgeRange::store($request, $item);

        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AgeRange  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = AgeRange::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->name}",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\AgeRange  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = AgeRange::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->name}",
        ]);
    }
}
