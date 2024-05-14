<?php

namespace App\Http\Controllers\Admin\ConservationFees;

use App\Http\Requests\Admin\ConservationFees\ConservationFeeStoreRequest;
use App\Http\Controllers\Controller;

use App\Models\ConservationFees\ConservationFee;

class ConservationFeeController extends Controller
{
    public function __construct() {
        $this->middleware('App\Http\Middleware\Admin\ConservationFees\ConservationFeeMiddleware', 
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
        return view('admin.conservation-fees.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.conservation-fees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConservationFeeStoreRequest $request)
    {
        $item = ConservationFee::store($request);

        $message = "You have successfully created {$item->name}";
        $redirect = $item->renderShowUrl();

        return response()->json([
            'message' => $message,
            'redirect' => $redirect,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = ConservationFee::withTrashed()->findOrFail($id);
        return view('admin.conservation-fees.show', [
            'item' => $item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ConservationFeeStoreRequest $request, $id)
    {
        $item = ConservationFee::withTrashed()->findOrFail($id);
        $message = "You have successfully updated {$item->name}";

        $item = ConservationFee::store($request, $item);

        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConservationFee  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = ConservationFee::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->name}",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\ConservationFee  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = ConservationFee::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->name}",
        ]);
    }
}
