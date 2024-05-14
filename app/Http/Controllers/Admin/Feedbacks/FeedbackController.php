<?php

namespace App\Http\Controllers\Admin\Feedbacks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Feedbacks\Feedback;

use App\Http\Requests\Admin\Feedbacks\FeedbackStoreRequest;

use App\Models\Answers\Answer;

use DB;

class FeedbackController extends Controller
{
    public function __construct() {
        $this->middleware('App\Http\Middleware\Admin\Feedbacks\FeedbackMiddleware', 
            ['only' => ['index', 'create', 'store', 'show', 'update', 'archive', 'restore', 'reOrder']]
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.feedbacks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.feedbacks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FeedbackStoreRequest $request)
    {
        DB::beginTransaction();

        $item = Feedback::store($request);

        if($request->answerable) {
            foreach ($request->answers as $key => $value) {
                $item->answers()->create([
                    'answer' => $value
                ]);
            }
        }

        DB::commit();

        $message = "You have successfully created {$item->renderQuestion()}";
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
        $item = Feedback::withTrashed()->findOrFail($id);
        return view('admin.feedbacks.show', [
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
    public function update(FeedbackStoreRequest $request, $id)
    {
        $item = Feedback::withTrashed()->findOrFail($id);

        DB::beginTransaction();
            $item = Feedback::store($request, $item);

            if($request->answerable) {
                foreach ($request->answers as $key => $value) {
                    $item->answers()->firstOrCreate([
                        'answer' => $value
                    ]);
                }
            }
        DB::commit();
        
        $message = "You have successfully updated {$item->renderQuestion()}";


        return response()->json([
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SurveyExperience  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $item = Feedback::withTrashed()->findOrFail($id);
        $item->archive();

        return response()->json([
            'message' => "You have successfully archived {$item->renderQuestion()}",
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\SurveyExperience  $sampleItem
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $item = Feedback::withTrashed()->findOrFail($id);
        $item->unarchive();

        return response()->json([
            'message' => "You have successfully restored {$item->renderQuestion()}",
        ]);
    }

    /*
     * Reorder the position of annual income 
     */
    public function reOrder(Request $request)
    {
        foreach ($request->items as $key => $item) {

            $expPos = Feedback::find($item['id']);

            if($expPos) {
                $expPos->update(['order' => $key ]);
            }

        }

        return response()->json([
            'message' => 'Successfully updated the order of survey experience questions',
        ]);
    }

    /*
     * Remove specific answer from survey experience 
     */

    public function answerRemove(Request $request)
    {
        DB::beginTransaction();
            $answer = Answer::withTrashed()->find($request->id);
            $answer->forceDelete();
        DB::commit();

        return response()->json([
            'message' => 200
        ]);
    }
}
