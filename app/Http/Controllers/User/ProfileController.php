<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function profile(){

           try{
                
            $request = json_decode(file_get_contents("php://input"));
            $user_access_key = $request->access_key;
            $userid=$request->id;
           
            if(!empty($user_access_key)){
                $user_id = DB::table('users')->where('access_key', $user_access_key)->where('user_id', $userid)->first();
               
                if($user_id){
                    $play_game = DB::table('play_games')->where('user_id', $user_id)->select('win', 'one', 'two', 'three', 'four', 'five', 'six', 'consi_id')->get()->toArray();
                    $consi_id=array_column($play_game, 'consi_id');
                    $values = array_count_values($consi_id);
                   
                    $win=array_column($play_game, 'win');
                    $win_sum = array_sum($win);
                    $one=array_column($play_game, 'one');
                    $one_sum = array_sum($one);
                    $per_one=($one_sum/$values[1])*100;
                    $two=array_column($play_game, 'two');
                    $two_sum = array_sum($two);
                    $per_two=($two_sum/$values[1])*100;
                    $three=array_column($play_game, 'three');
                    $three_sum = array_sum($three);
                    $per_three=($three_sum/$values[1])*100;
                    $four=array_column($play_game, 'four');
                    $four_sum = array_sum($four);
                    $per_four=($four_sum/$values[1])*100;
                    $five=array_column($play_game, 'five');
                    $five_sum = array_sum($five);
                    $per_five=($five_sum/$values[1])*100;
                    $six=array_column($play_game, 'six');
                    $six_sum = array_sum($six);
                    $per_six=($six_sum/$values[1])*100;
                                        
                    $result=[
                         "data"=>[
                           "count_consi_id"=>$values[1],
                           "per_one"=>$per_one,
                           "per_two"=>$per_two,
                           "per_three"=>$per_three,
                           "per_four"=>$per_four,
                           "per_five"=>$per_five,
                           "per_six"=>$per_six,
                           "win_sum"=>$win_sum,

                         ],
                    ];
                    
                    return response()->json($result);
                }
            }else{
                return response()->json(["status"=>201,"message"=>trans('messages.API Key comporsory')],201);
            }
            
           }catch(\Exception $e){
                
           }
    }
}
