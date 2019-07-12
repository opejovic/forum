<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\ThreadReceivedNewReply;
use App\Notifications\YouHaveBeenMentioned;

class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param ThreadReceivedNewReply $event
     *
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        $event->reply->mentionedUsers()
            ->each->notify(new YouHaveBeenMentioned($event->reply));
    }
}
