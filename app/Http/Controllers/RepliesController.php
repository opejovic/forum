<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Thread;
use App\Inspections\Spam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $channelId
     * @param \App\Models\Thread $thread
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $channelId
     * @param \App\Models\Thread $thread
     *
     * @param \App\Spam $spam
     *
     * @return \Illuminate\Http\Response
     */
    public function store($channelId, Thread $thread, Spam $spam)
    {
        request()->validate([
            'body' => ['required', 'min:2'],
        ]);

        $spam->detect(request('body'));

        $reply = $thread->addReply([
            'user_id' => Auth::user()->id,
            'body' => request('body'),
        ]);

        if (request()->wantsJson()) {
            return response($reply->load('owner'), 200);
        }

        return back()->with('flash', 'Your reply has been posted.');;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->update(request(['body']));

        if (request()->wantsJson()) {
            return response(['message' => 'Reply updated!'], 200);
        }

        return back()->with('message', 'Reply updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reply  $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->wantsJson()) {
            return response(['message' => 'Reply deleted.'], 200);    
        }

        return back()->with('flash', 'Reply deleted.');
    }
}
