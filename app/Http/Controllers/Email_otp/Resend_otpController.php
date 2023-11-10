<?php

namespace App\Http\Controllers\Email_otp;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Resend_otpController extends Controller
{
    public function Resend_otp()
    {

        try {
            $request = json_decode(file_get_contents("php://input"));
            $userEmail = $request->email;
            $user_access_key = $request->access_key;
            $updated_at=time();
            
            if ($userEmail && $user_access_key) {
                $user_email = DB::table('users')->where('email', $userEmail)->where('access_key', $user_access_key)->select('email', 'otp', 'access_key')->first();
                              
                if ($user_email) {
                    if($user_email->otp > 99999)
                    {
                        $otp=$user_email->otp;
                    }
                    else{
                        $otp = mt_rand(111111, 999999);
                    }
                    $checkuser = DB::table('users')->where('email', $userEmail)->update([
                        'otp' => $otp, 'updated_at'=>$updated_at]);
                    if ($checkuser) {
                        $this->generateOTP($userEmail, $otp);
                        $result['status'] = 200;
                        $result['response'] = "Sucess";
                        $result['data'] = $otp;
                        return response()->json($result, 200);
                    } else {
                        $result['status'] = 201;
                        $result['response'] = "Eror please try again.";
                        return response()->json($result, 201);
                    }                     
                } else {
                    return response()->json(["status" => 201, "message" => trans('messages.Usermail is incorrect')], 201);
                }

                
            } else {
                return response()->json(["status" => 201, "message" => trans('messages.Usermail,acess_key comporsory')], 201);
            }
        } catch (\Exception $e) {
            return response()->json(["status" => 201, "message" => trans('messages.Usermail,acess_key comporsory')], 201);

        }
    }
}
