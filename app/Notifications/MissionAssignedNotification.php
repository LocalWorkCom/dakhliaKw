<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\instantmission;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MissionAssignedNotification extends Notification
{
    use Queueable;
    protected $mission;
    /**
     * Create a new notification instance.
     */
    public function __construct(instantmission $mission)
    {
        $this->mission = $mission;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
{
    return (new MailMessage)
                ->line('A new mission has been assigned to your team.')
                ->action('View Mission', url('/missions/'.$this->mission->id))
                ->line('Thank you for using our application!');
}

public function toArray($notifiable)
{
    return [
        'mission_id' => $this->mission->id,
        'title' => $this->mission->title,
        'description' => $this->mission->description,
    ];
}
}
