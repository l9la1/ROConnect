<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCSignaling implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $senderId;
    public $receiverId;
    public $data;

    public function __construct($senderId, $receiverId, $data)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('voice-call-channel'),
        ];
    }
}
