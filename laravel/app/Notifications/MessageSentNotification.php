<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageSentNotification extends Notification implements ShouldQueue
{
  use Queueable;
  public $chatMessage;
  public $subjectPrefix;

  /**
   * Create a new notification instance.
   * @param  array  $chatMessage
   * [
   *   'sent_user' => $chatMessage->createdBy->full_name,
   *   'chat_room_id' => $chatMessage->chatRoom->id,
   *   'chat_room_name' => $chatMessage->chatRoom->name,
   *   'created_at' => $chatMessage->created_at,
   *   'message_body' => Str::limit($chatMessage->body, 20, '（...）'),
   * ]
   * @return void
   */
  public function __construct(array $chatMessage)
  {
    $this->chatMessage = $chatMessage;
    // $this->queue = 'message_sent';
    $this->subjectPrefix = config('app.name');
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->subject("【{$this->subjectPrefix}】新着メッセージが届きました")
      ->markdown('mail.chat_message.sent', [
        'chatMessage' => $this->chatMessage,
        'notifiable' => $notifiable,
        'detailUrl' => config('app.front_url', 'http://localhost:3000') . '/mypage/chat/' . $this->chatMessage['chat_room_id'],
      ]);
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
