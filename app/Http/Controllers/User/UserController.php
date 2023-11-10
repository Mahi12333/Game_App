<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function User()
    {
        try {
            $request = json_decode(file_get_contents("php://input"));
            $userEmail = $request->email;
            $user_access_key = $request->access_key;
            $user_Name = $request->user_name;


            if (!empty($userEmail) && !empty($user_access_key) && !empty($user_Name)) {

                $user_status = DB::table('users')->where('email', $userEmail)->where('access_key', $user_access_key)->first();
                if (!empty($user_status) && $userEmail === $user_status->email && $user_access_key === $user_status->access_key) {
                    if ($user_status->status > 0) {

                        DB::table('users')->where('email', $userEmail)->where('access_key', $user_access_key)->update([
                            'firstname' => $user_Name,
                        ]);
                        return response()->json('sucess', 200);
                    } else {
                        return response()->json(["status" => 201, "message" => "Please Compelete the registration Process"], 201);
                    }
                } else {
                    return response()->json(["status" => 201, "message" => "Email and Access_Key dose not match did not match"], 201);
                }
            } else {

                return response()->json(["status" => 201, "message" => trans('messages.Usermail,acess_key are comporsory')], 201);
            }
        } catch (\Exception $e) {

            return response()->json(["status" => 201, "message" => trans('messages.Usermail,acess_key, User Name are comporsory')], 201);
        }
    }
}
