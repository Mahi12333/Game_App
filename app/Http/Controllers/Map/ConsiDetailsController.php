<?php

namespace App\Http\Controllers\Map;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\HttpCache\ResponseCacheStrategy;

class ConsiDetailsController extends Controller
{

    public function constituencyDetails($id,$user_id,$attempt_count,$specific_state_id)
    {
        try {
            
            $constituency = DB::table('constituencies_list')
                ->select('id', 'constituencies_name', 'state', 'lat', 'longt')
                ->where('id', $id)
                ->first();
                $consi_id=$constituency->id;
               

            if (!$constituency) {
                return response()->json(["status" => 201, "message" => "Constituency not found"]);
            }


            $distance = $this->calculateDistance($constituency->lat, $constituency->longt);


            $direction = $this->calculateDirection($constituency->lat, $constituency->longt);

            
            if(!empty($user_id)){   
                $user=DB::table('play_games')->where('user_id',$user_id)->where('consi_id',$consi_id)->value('user_id');
                 if($user){
                    DB::table('play_games')->where('user_id',$user)->where('consi_id',$consi_id)->update([
                       'attempt_count'=>$attempt_count,
                    ]);
                      
                 }else{
                    $user_insert=DB::table('play_games')->insert([
                        'user_id'=>$user_id,
                        'consi_id'=>$consi_id,
                        'attempt_count'=>$attempt_count
                     ]);

                 }
             }else{
                return response()->json(["status" => 201, "message" => "User can not be empty"]);
             }

             if($consi_id==$specific_state_id){
                $result = [
                    'status' => 200,
                    'response' => 'Constituency details',
                    'data' => [
                        'name' => $constituency->constituencies_name,
                        'distance_km' => $distance,
                        'direction' => $direction,
                        'match'=>"sucess",
                    ],
                ];

             }else{
                
                $result = [
                    'status' => 200,
                    'response' => 'Constituency details',
                    'data' => [
                        'name' => $constituency->constituencies_name,
                        'distance_km' => $distance,
                        'direction' => $direction,
                    ],
                ];

             }
             
            
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(["status" => 500, "message" => "An error occurred"], 500);
        }
    }



    // You can implement your own logic for calculating distance and direction here.

    private function calculateDistance($lat, $longt)
    {
        $earthRadius = 6371;

        $lat1 = deg2rad(18.296974);
        $lon1 = deg2rad(83.896782);
        $lat2 = deg2rad($lat);
        $lon2 = deg2rad($longt);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c ;

        return $distance;
    }

    // Implement your direction calculation logic here.
    private function calculateDirection($lat, $longt)
    {
        $lat1 = deg2rad(18.296974);
        $lon1 = deg2rad(83.896782);
        $lat2 = deg2rad($lat);
        $lon2 = deg2rad($longt);

        $dLon = $lon2 - $lon1;

        $y = sin($dLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($dLon);

        $bearing = atan2($y, $x);
        $bearing = rad2deg($bearing);
        $bearing = ($bearing + 360) % 360; // Normalize to [0, 360) degrees

        return $bearing;
    }
}
