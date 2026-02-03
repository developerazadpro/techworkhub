<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\JobStatusChanged;
use App\Models\Notification;

class SendJobStatusChangedNotification
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
    public function handle(JobStatusChanged $event)
    {
        $job = $event->job;

        $messageMap = [
            'assigned'    => 'Your job has been assigned',
            'in_progress' => 'Work on your job has started',
            'completed'   => 'Your job has been completed',
            'cancelled'   => 'Your job has been cancelled',
        ];

        if (! isset($messageMap[$event->newStatus])) {
            return;
        }

        Notification::create([
            'user_id' => $job->client_id,
            'type' => 'job_status_changed',
            'title' => 'Job status updated',
            'body' => $messageMap[$event->newStatus],
            'data' => [
                'job_id' => $job->id,
                'status' => $event->newStatus,
                'route' => "/client/job/{$job->id}",
            ],
        ]);
    }
}
