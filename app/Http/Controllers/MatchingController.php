<?php

namespace App\Http\Controllers;

use App\Models\UserSessions;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    function index()
    {
        $hobbies= request()->query('hobbies');
        $minHob= request()->query('minHob');
        if(isset($hobbies)) {
            $hobbies = json_decode($hobbies);
            if(isset($minHob)&&$minHob>count($hobbies))return response(['error'=>'The minHob must be lower or equal to the length of hobbies'],400);

            // First filtering
            $sessions = UserSessions::whereLike('interest_tag', "%" . $hobbies[0] . "%");
            for ($i = 1; $i < count($hobbies); $i++) {
                $sessions->orWhereLike('interest_tag', "%" . $hobbies[$i] . "%");
            }

            $sessions = $sessions->get();

            $potmatch = [];
            foreach ($sessions as $session) {
                $interests=explode(',', $session->interest_tag);
                // Check difference on hobbies
                $notMatchings =  array_diff($hobbies,$interests);
                $notMatchingCount = count($notMatchings);
                
                // Check difference on interests
                $notMatchinsi=array_diff($interests,$hobbies);
                $notMatchingsiCount=count($notMatchinsi);

                if(isset($minHob)&&count($hobbies)-$notMatchingCount<(int)$minHob)continue;
                array_push($potmatch,  [
                    'user' => $session,
                    'amount' => ((count($hobbies) + count($interests)) - ($notMatchingCount + $notMatchingsiCount)) / (count($hobbies) + count($interests)) * 100,
                    'notMatchingHobbies' => implode(',', array_merge($notMatchings, $notMatchinsi))
                ]);
            }
            
            return response()->json(['match'=>$potmatch]);
        }else{
            return response()->view('matching');
        }
    }
}
