<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements MustVerifyEmail
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password', 'avatar_path'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token', 'email',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	/**
	 * Route key name for the model.
	 *
	 */
	public function getRouteKeyName()
	{
		return 'name';
	}

	/**
	 * User has many threads.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function threads()
	{
		return $this->hasMany(Thread::class)->latest();
	}

	/**
	 * User has one last reply.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function lastReply()
	{
		return $this->hasOne(Reply::class)->latest();
	}

	/**
	 * User has many activities.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function activities()
	{
		return $this->hasMany(Activity::class);
	}

	/**
	 * User can publish a thread.
	 *
	 * @param $attributes
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function publishThread($attributes)
	{
		return $this->threads()->create($attributes);
	}

	/**
	 * User can have many replies.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function replies()
	{
		return $this->hasMany(Reply::class);
	}

	/**
	 * Get the cache key for when the user visited thread.
	 *
	 * @param $thread
	 *
	 * @return string
	 */
	public function visitedThreadCacheKey($thread)
	{
		return sprintf("users.%s.visits.%s", Auth::user()->id, $thread->id);
	}

	/**
	 * User read the thread.
	 *
	 * @param $thread
	 *
	 * @throws \Exception
	 */
	public function read($thread)
	{
		cache()->forever($this->visitedThreadCacheKey($thread), now());
	}

	/**
	 * Get the users avatar path.
	 *
	 * @return string
	 **/
	public function getAvatarPathAttribute($avatar)
	{
		return $avatar ? asset("storage/{$avatar}") : asset('storage/avatars/default.png');
	}
}
