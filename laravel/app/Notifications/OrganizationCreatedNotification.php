<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizationCreatedNotification extends Notification implements ShouldQueue
{
  use Queueable;
  public $organizaiton;
  public $subjectPrefix;
  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct()
  {
    // $this->queue = 'organization_shared';
    $this->subjectPrefix = config('app.name');
  }

  /**
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
    $query = base64_encode($notifiable->email);
    return (new MailMessage)
      ->subject("【{$this->subjectPrefix}】組織情報の登録が完了しました")
      ->markdown('mail.organization.created', [
        'notifiable' => $notifiable,
        'mypageUrl' => config('app.front_url', 'http://localhost:3000') . '/mypage',
        'adminFrontUrl' => config('app.admin_front_url', 'http://localhost:3001') . "/account_verification?n={$query}",
        'adminSigninUrl' => config('app.admin_front_url', 'http://localhost:3001') . '/signin',
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
