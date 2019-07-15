<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Channel;
use App\Rules\SpamFree;
use App\Utilities\Trending;
use Illuminate\Http\Request;
use App\Filters\ThreadFilters;
use Illuminate\Support\Facades\Auth;

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
	public function index(Channel $channel, ThreadFilters $filters, Trending $trending)
	{
		return view('threads.index', [
			'threads' => $this->getThreads($filters, $channel),
			'trendingThreads' => $trending->get(),
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
	public function show($channelId, Thread $thread, Trending $trending)
	{
		// Record that the user visited this page.
		if (Auth::check()) {
			Auth::user()->read($thread);
		}

		$trending->push($thread);
		$thread->increment('visits');

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
	 * Get the threads accoring to the given filters.
	 *
	 * @param $filters
	 * @param null $channel
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
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
