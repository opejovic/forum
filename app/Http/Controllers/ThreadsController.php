<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Channel;
use App\Rules\SpamFree;
use Illuminate\Http\Request;
use App\Filters\ThreadFilters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ThreadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Channel $channel
     * @param \App\Filters\ThreadFilters $filters
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $filters)
    {
		$trending = array_map('json_decode', Redis::zrevrange('trending_threads', 0, 4));

        return view('threads.index', [
			'threads' => $this->getThreads($filters, $channel),
			'trendingThreads' => $trending,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'channel_id' => ['required', 'exists:channels,id'],
            'title'      => ['required', 'min:2', new SpamFree],
            'body'       => ['required', 'min:2', new SpamFree],
        ]);

        $thread = Auth::user()->publishThread([
            'channel_id' => request('channel_id'),
            'title'      => request('title'),
            'body'       => request('body'),
        ]);

        return redirect(route('threads.show', [$thread->channel->slug, $thread->id]))
            ->with('flash', 'Thread successfully published.');
    }

    /**
     * Display the specified resource.
     *
     * @param  $channelId
     * @param \App\Models\Thread $thread
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function show($channelId, Thread $thread)
    {
        // Record that the user visited this page.
        if (Auth::check()) {
            Auth::user()->read($thread);
		}
		
		Redis::zincrby('trending_threads', 1, json_encode([
			'title' => $thread->title,
			'path' => $thread->path(),
		]));

        return view('threads.show', [
            'thread' => $thread,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Thread $thread
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Thread $thread
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Channel $channel
     * @param \App\Models\Thread $thread
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Channel $channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 200);
        }

        return redirect(route('threads.index'))->with('flash', 'Thread deleted.');
    }

    /**
     * summary
     *
     * @param $filters
     * @param null $channel
     *
     * @return void
     * @author
     */
    public function getThreads($filters, $channel = null)
    {
        $threads = Thread::filter($filters)->latest();

        if ($channel->exists) {
            $threads = $channel->threads()->latest();
        }

        return $threads->paginate(15);
    }
}
