<?php

namespace App\Http\Controllers\Map;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MapController extends Controller
{
    public function map()
    {
        try {
            $request = json_decode(file_get_contents("php://input"));
            $user_id = $request->id;
            $access_key = $request->access_key;

            if (!empty($user_id) && !empty($access_key)) {
                $user = DB::table('play_games')->select('consi_id')->where('user_id', $user_id)->get()->toArray();//array convert
                $arrcons=array_column($user, 'consi_id');

                $urls=env('consti_url');
                $constituencies = DB::table('constituencies_list')
                        ->select('id', 'constituencies_name', DB::raw("CONCAT('$urls', constituencies_img) as maps"), DB::raw("CONCAT('$urls', hint_img) as hint_img"), 'state','lat','longt','current_mp','current_party','previous_mp','previous_party','famous_personality_1','famous_personality_2','famous_festival','famous_landmark','status')
                        ->where('status', 1);
                if(count($arrcons)> 0)
                {
                    $constituencies =$constituencies->whereNotIn('id', $arrcons);
                }    
                
                $constituencies =$constituencies->orderBy('id', 'asc')
                    ->limit(1)
                    ->first();
                if ($constituencies) {
                    $result['status']=200;
                    $result['responce']='Constituecies list';
                    $result['data']=$constituencies;
                    return response()->json($result, 200);
                }
                else{
                    if(count($arrcons)>0)
                    {
                        return response()->json(["status" => 204, "message" => "Today, You already played all match"]);
                    }
                    else{
                        return response()->json(["status" => 204, "message" => "No constituencies available"]);
                    }
                     
                }
            }
            else {

                return response()->json(["status" => 201, "message" => trans('messages.Please Enter Vaid User_id and Access_key')], 201);               
            }
        } catch (\Exception $e) {
            return response()->json(["status" => 201, "message" => trans('messages.Please Enter Vaid User_id and Access_key')], 201);
        }
    }
}
