<?php

namespace App\Http\Controllers\Admin\Sources;

use App\Http\Requests\Admin\Sources\SourceStoreRequest;

use App\Http\Controllers\Controller;

use App\Models\Sources\Source;

class SourceController extends Controller
{

    public function __construct() {
        $this->middleware('App\Http\Middleware\Admin\Sources\SourceMiddleware', 
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
        return view('admin.sources.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Source::withTrashed()->findOrFail($id);
        return view('admin.sources.show', [
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
    public function update(SourceStoreRequest $request, $id)
    {
        $item = Source::withTrashed()->findOrFail($id);
        $message = "You have successfully updated {$item->name}";

        $item = Source::store($request, $item);

        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Source  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = Source::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->name}",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Source  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = Source::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->name}",
        ]);
    }
}
