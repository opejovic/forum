<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

	/**
	 * Determine whether the user can update the user.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Models\User  $authUser
	 * @return mixed
	 */
	public function update(User $user, User $authUser)
	{
		return $user->is($authUser);
	}
}
