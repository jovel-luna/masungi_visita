<?php

namespace App\Http\Controllers\Admin\Copywritings;

use App\Http\Requests\Admin\Copywritings\CopywritingStoreRequest;

use App\Http\Controllers\Controller;

use App\Models\Copywritings\Copywriting;

class CopywritingController extends Controller
{
    public function __construct() {
        $this->middleware('App\Http\Middleware\Admin\Copywritings\CopywritingMiddleware', 
            ['only' => ['index', 'show', 'update', 'archive', 'restore']]
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.copywritings.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Copywriting::withTrashed()->findOrFail($id);
        return view('admin.copywritings.show', [
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
    public function update(CopywritingStoreRequest $request, $id)
    {
        $item = Copywriting::withTrashed()->findOrFail($id);
        $message = "You have successfully updated {$item->name}";

        $item = Copywriting::store($request, $item);

        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Copywriting  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = Copywriting::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->name}",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Copywriting  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = Copywriting::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->name}",
        ]);
    }
}
