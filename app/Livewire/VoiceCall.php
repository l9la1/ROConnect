<?php

namespace App\Livewire;

use App\Events\WebRTCSignaling;
use Livewire\Component;

class VoiceCall extends Component
{
    public $userId;
    public $turnUsername;
    public $turnCredential;

    public function mount()
    {
        $this->userId = 'user_' . uniqid();
        $this->turnUsername = config('services.turn.username');
        $this->turnCredential = config('services.turn.credential');
    }

    public function sendSignal($receiverId, $data)
    {
        broadcast(new WebRTCSignaling($this->userId, $receiverId, $data))->toOthers();
    }

    public function render()
    {
        return view('livewire.voice-call');
    }
}
