<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\FcmOptions;
use NotificationChannels\Fcm\Resources\Notification as FcmNotificationResource;

class FcmNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */

     public function via($notifiable)
     {
        return [FcmChannel::class]; // Make sure this is FcmChannel::class and not just 'fcm'
    }

     public function toFcm($notifiable)
     {
         return FcmMessage::create()
             ->setFcmOptions(FcmOptions::create()->setAnalyticsLabel('promo_alerts'))
             ->setNotification(
                 FcmNotificationResource::create()
                     ->setTitle($this->title)
                     ->setBody($this->body)
             );
     }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
