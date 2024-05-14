<?php

namespace App\Http\Controllers\Admin\GeneratedEmails;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;

use App\Models\Emails\GeneratedEmail;

use App\Http\Requests\Admin\GeneratedEmails\GeneratedEmailStoreRequest;

class GeneratedEmailController extends Controller
{
    public function __construct() {
        $this->middleware('App\Http\Middleware\Admin\GeneratedEmails\GeneratedEmailMiddleware', 
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
        return view('admin.generated-emails.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.generated-emails.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\GeneratedEmailStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GeneratedEmailStoreRequest $request)
    {
        $genericEmailExists = GeneratedEmail::where('notification_type', $request->notification_type)
            ->whereNull('experience_id')
            ->count();

        if($genericEmailExists > 1  && !$request->experience_id) {
            throw ValidationException::withMessages([
                'generic_email_exists' => ['Generic email for this email type already exists']
            ]); 
        } else {
            $item = GeneratedEmail::store($request);
        }

        $message = "You have successfully created {$item->title}";
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
        $item = GeneratedEmail::withTrashed()->findOrFail($id);
        return view('admin.generated-emails.show', [
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
     * @param  \Illuminate\Http\GeneratedEmailStoreRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GeneratedEmailStoreRequest $request, $id)
    {
        $genericEmailExists = GeneratedEmail::where('notification_type', $request->notification_type)
            ->whereNull('experience_id')
            ->count();

        if($genericEmailExists > 1 && !$request->experience_id) {
            throw ValidationException::withMessages([
                'generic_email_exists' => ['Generic email for this email type already exists']
            ]); 
        } else {
            $item = GeneratedEmail::withTrashed()->findOrFail($id);
        }
        $message = "You have successfully updated {$item->title}";

        $item = GeneratedEmail::store($request, $item);

        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Emails\GeneratedEmail  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = GeneratedEmail::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->title}",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Models\Emails\GeneratedEmail  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = GeneratedEmail::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->title}",
        ]);
    }
}
