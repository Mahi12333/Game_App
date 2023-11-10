<?php

namespace App\Http\Controllers\Email_otp;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Casts\Json;

class Email_otpController extends Controller
{

    public function Email_otp()
    {

        $request = json_decode(file_get_contents("php://input"));
        $media = isset($request->media) && !empty($request->media) ? $request->media :null;
        $updated_at=time();
        if (empty($request->email)) {
            return response()->json(["status" => 201, "message" => trans('messages.Email Id comporsory')], 201);
        }

        $Email = $request->email;

        $Existing_email = DB::table('users')->where('email', $Email)->value('email');
        
        $otp = mt_rand(111111, 999999);
        $apiKey = Str::random(32);
        if ($Existing_email) {
            

            $checkuser=DB::table('users')->where('email', $Email)->update([
                'otp' => $otp,
                'access_key'=>$apiKey,
                'updated_at'=>$updated_at
            ]);
            
        }
        else{                        
            
            $checkuser=DB::table('users')->insert([
                'email' => $Email,
                'otp' => $otp,
                'access_key'=>$apiKey,
                'updated_at'=>$updated_at,
                'media_login_type'=>$media

            ]);
            
        }
       if($checkuser)
       {
            $this->generateOTP($Email, $otp);
            $result['status'] = 200;
            $result['response'] = "Sucess";
            $result['access_key'] = $apiKey;
            return response()->json($result, 200);
       }
       else{
            $result['status'] = 201;
            $result['response'] = "Eror please try again.";
            return response()->json($result, 201);
       }
        
    }
}
