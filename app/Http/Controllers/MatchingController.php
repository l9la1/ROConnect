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

            $potmatch = null;
            $noMatchc = null;
            foreach ($sessions as $session) {
                $notMatchings =  array_diff($hobbies,explode(',', $session->interest_tag));
                $notMatchingCount = count($notMatchings);
                if(isset($minHob)&&count($hobbies)-$notMatchingCount<(int)$minHob)continue;
                if ($notMatchingCount === 0) {
                    $potmatch = $session;
                    $noMatchc=count($hobbies);
                    break;
                }
                if (!isset($noMatchc)) {
                    $noMatchc = $notMatchingCount;
                    $potmatch = $session;
                }
                else {
                    if ($noMatchc > $notMatchingCount) {
                        $noMatchc = $notMatchingCount;
                        $potmatch = $session;
                    }
                }
            }
            
            return response()->json(['match'=>$potmatch,'amount'=>$noMatchc/count($hobbies)*100,'notMatchingHobbies'=>implode(',',$notMatchings)]);
        }else{
            return response()->view('matching');
        }
    }
}
