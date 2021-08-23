<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\ChatRoom;

class MessageSent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $flag;
  public $message;
  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct(ChatMessage $message, string $flag)
  {
    $this->message = $message;
    $this->flag = $flag;
  }

  // public function broadcastAs()
  // {
  //   return 'message.sent';
  // }
  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new Channel('chat', $this->message, $this->flag);
  }

  // public function broadcastWhen()
  // {
  //   // return false;
  // }
}
