<?php

namespace App\Http\Controllers\Email_otp;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Verify_otpController extends Controller
{
    public function Verify_otp()
    {
        try {

            $request = json_decode(file_get_contents("php://input"));
            $userEmail = $request->email;
            $userotp = $request->otp;
            $user_access_key = $request->access_key;
            $updated_at=time();
            if ($userEmail && $user_access_key && $userotp) {

                $dbotp = DB::table('users')->where('email', $userEmail)->where('access_key', $user_access_key)->select('updated_at','otp')->first();
                if(empty($dbotp))
                {
                    return response()->json(["status" => 201, "message" => trans('messages.User Mail dose not Match')], 201); 

                }
                $comtime=$updated_at-$dbotp->updated_at;
                
                if($comtime > 240)
                {
                    return response()->json(["status" => 201, "message" => trans('messages.Otp already Expaired')], 201); 

                }

                if($dbotp->otp==0)
                {
                    return response()->json(["status" => 201, "message" => trans('messages.Otp already used')], 201); 
                }

                if ($userotp === $dbotp->otp) {
                    DB::table('users')->where('email', $userEmail)->update([
                        'otp' => 0,
                        'status'=>1,                      
                     ]);
                    $result['response'] = "Sucess";
                    return response()->json($result, 200);
                } else {
                    return response()->json(["status" => 201, "message" => trans('messages.Otp dose not match')], 201);
                }
            } else {
                return response()->json(["status" => 201, "message" => trans('messages.Usermail, otp,acess_key comporsory')], 201);
            }
        } catch (\Exception $e) {
            return response()->json(["status" => 201, "message" => trans('messages.Usermail, otp,acess_key comporsory')], 201);
        }
    }
}
