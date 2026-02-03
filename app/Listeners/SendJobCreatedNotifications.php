<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\JobCreated;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class SendJobCreatedNotifications
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
    public function handle(JobCreated $event): void
    {
        Log::info('JobCreated listener fired', [
            'job_id' => $event->job->id,
        ]);

        $job = $event->job;

        $technicianIds = collect($job->recommended_technicians);

        foreach ($technicianIds as $techId) {
            Notification::create([
                'user_id' => $techId,
                'type' => 'job_created',
                'title' => 'New job matches your skills',
                'body' => "A new job is available that matches your profile.",
                'data' => [
                    'job_id' => $job->id,
                    'route' => "/technician/job/{$job->id}",
                ],
            ]);
        }
    }
}
