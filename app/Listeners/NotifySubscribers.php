<?php

namespace App\Listeners;

class NotifySubscribers
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $event->reply->thread->notifySubscribers($event->reply);
    }
}
