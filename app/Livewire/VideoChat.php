<?php

namespace App\Livewire;

use App\Events\WebRTCSignaling;
use App\Models\UserSessions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class VideoChat extends Component
{
    public $userId;
    public $receiverId;
    public $activeInterests = [];
    public $turnUsername;
    public $turnCredential;

    public function mount()
    {
            $this->activeInterests = request()->query('interests', []);
            $this->turnUsername = config('services.turn.username');
            $this->turnCredential = config('services.turn.credential');
        if(!Session::get('session')) {
            $this->userId = 'user_' . uniqid();
            $ses = UserSessions::create([
                'session_id' => $this->userId,
                'interest_tag' => implode(',', $this->activeInterests),
                'display_name' => $this->turnUsername,
            ]);

        }else
        {
            $this->userId = Session::get('session')['session_id'];
            $ses=UserSessions::where('session_id',$this->userId)->first();
            if($ses)
            {
            $ses->interest_tag=implode(',',$this->activeInterests);
            $ses->save();
            }
        }
            session(['session'=>$ses->toArray()]);
            $this->getReceiver();
    }

    public function sendSignal($receiverId, $data)
    {
        broadcast(new WebRTCSignaling($this->userId, $this->receiverId, $data))->toOthers();
    }

    public function broadcastReady()
    {
        broadcast(new WebRTCSignaling($this->userId,'all', ['type' => 'ready']))->toOthers();
    }

    private function getReceiver(){
        
            $sessions = UserSessions::whereLike('interest_tag', "%" . $this->activeInterests[0] . "%")->whereNot('session_id',$this->userId);
            for ($i = 1; $i < count($this->activeInterests); $i++) {
                $sessions->orWhereLike('interest_tag', "%" . $this->activeInterests[$i] . "%");
            }

            $sessions = $sessions->get();

            foreach ($sessions as $session) {
                $interests=explode(',', $session->interest_tag);
                $minAmount=1;
                $notMatchings =  array_diff($this->activeInterests,$interests);
                $notMatchingCount = count($notMatchings);
                // if(count($this->activeInterests)-$notMatchingCount>=$minAmount){
                if(true){
                $this->receiverId=$session->session_id;
                break;
                }
            }
            
    }
    public function render()
    {
        return view('livewire.video-chat');
    }
}
