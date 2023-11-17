<?php

namespace App\Http\Controllers\Map;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\HttpCache\ResponseCacheStrategy;


class ConsiDetailsController extends Controller
{
    public function constituencyDetails()
    {

        try {
            $request = json_decode(file_get_contents("php://input"));
            $search_id = $request->id;
            $user_id = $request->user_id;
            $specific_state_id = $request->specific_state_id;

            if (!empty($search_id) && !empty($user_id) && !empty($specific_state_id)) {

                $constituency = DB::table('constituencies_list')
                    ->select('id', 'constituencies_name', 'state', 'lat', 'longt')
                    ->where('id', $search_id)
                    ->first();
                $map_id = $constituency->id;


                $specific_state_distance = DB::table('constituencies_list')
                    ->select('lat', 'longt')
                    ->where('id', $specific_state_id)
                    ->first();

             

                if (!$constituency) {
                    return response()->json(["status" => 201, "message" => "Constituency not found"]);
                }

                if (!$specific_state_distance) {
                    return response()->json(["status" => 201, "message" => "Constituency Distance not found"]);
                }


                $distance = $this->calculateDistance($constituency->lat, $constituency->longt, $specific_state_distance->lat, $specific_state_distance->longt);


                $direction = $this->calculateDirection($constituency->lat, $constituency->longt, $specific_state_distance->lat, $specific_state_distance->longt);


                if (!empty($user_id)) {
                    $user = DB::table('play_games')->where('user_id', $user_id)->value('user_id');
                    if ($user) {
                        $dbattempt_count = DB::table('play_games')->where('consi_id', $specific_state_id)->select('attempt_count', 'sear_id')->get()->toArray();
                        $searc_id = array_column($dbattempt_count, 'sear_id');
                        // $attempt_count=$dbattempt_count->attempt_count;

                        if (count($searc_id) > 0) {

                            if (in_array($search_id, $searc_id)) {
                                return response()->json("You are already search at before");
                            } else {
                                $fff = DB::table('play_games')->where('user_id', $user)->where('consi_id', $specific_state_id)->insert([
                                    'user_id' => $user,
                                    'consi_id' => $specific_state_id,
                                    'sear_id' => $search_id,
                                    // 'attempt_count' => $attempt_count + 1,
                                ]);
                            }

                            // return response()->json($fff);
                        }
                    } else {
                        $dbattempt_count = DB::table('play_games')->where('consi_id', $specific_state_id)->value('attempt_count');
                        $user_insert = DB::table('play_games')->insert([
                            'user_id' => $user_id,
                            'consi_id' => $map_id,
                            'sear_id' => $search_id,
                            // 'attempt_count' => $dbattempt_count + 1,
                        ]);
                    }
                } else {
                    return response()->json(["status" => 201, "message" => "User can not be empty"]);
                }

                if ($search_id == $specific_state_id) {

                    $win = 1;
                    $specificValue = 0;
                    switch ($search_id) {
                        case 1:
                            $specificValue = 'one';
                            break;
                        case 2:
                            $specificValue = 'two';
                            break;
                        case 3:
                            $specificValue = 'three';
                            break;
                        case 4:
                            $specificValue = 'four';
                            break;
                        case 5:
                            $specificValue = 'five';
                            break;
                        case 6:
                            $specificValue = 'six';
                            break;
                    }
                $result=[
                    'data' => [
                        'name' => $user,
                        'distance_km' => $search_id,
                        'direction' => $specific_state_id,
                        
                    ],
                ];

                DB::table('play_games')
                        ->where('user_id', $user)
                        ->where('sear_id', $search_id)
                        ->where('consi_id', $specific_state_id)
                        ->update([$specificValue => 1, 'win' => $win]);
                 
                    $result = [
                        'status' => 200,
                        'response' => 'Constituency details',
                        'data' => [
                            'name' => $constituency->constituencies_name,
                            'distance_km' => $distance,
                            'direction' => $direction,
                            'match' => "sucess",
                        ],
                    ];


                    // return response()->json($result, 200);
                } else {

                    $result = [
                        'status' => 200,
                        'response' => 'Constituency details',
                        'data' => [
                            'name' => $constituency->constituencies_name,
                            'distance_km' => $distance,
                            'direction' => $direction,
                        ],
                    ];

                    return response()->json($result, 200);
                }
            } else {
                return response()->json("faild", 201);
            }
        } catch (\Exception $e) {
            return response()->json(["status" => 500, "message" => "An error occurred"], 500);
        }
    }


    // You can implement your own logic for calculating distance and direction here.

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {

        $earthRadius = 6371;


        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);


        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;


        $a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }


    // Implement your direction calculation logic here.
    private function calculateDirection($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLon = $lon2 - $lon1;

        $y = sin($dLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($dLon);

        $bearing = atan2($y, $x);
        $bearing = rad2deg($bearing);
        $bearing = ($bearing + 360) % 360;

        return $bearing;
    }
}
