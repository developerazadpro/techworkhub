<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\JobAccepted;
use App\Models\Notification;

class SendJobAcceptedNotifications
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JobAccepted $event): void
    {
        Notification::create([
            'user_id' => $event->job->client_id,
            'type' => 'job_accepted',
            'title' => 'Your job was accepted',
            'body' => "{$event->technician->name} accepted your job.",
            'data' => [
                'job_id' => $event->job->id,
                'technician_id' => $event->technician->id,
                'route' => "/client/job/{$event->job->id}",
            ],
        ]);
    }
}
