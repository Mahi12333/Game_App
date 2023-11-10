<?php

namespace App\Http\Controllers\Language;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    // view page

    // public function changeLanguage(Request $request)
    public function changeLanguage()
    {
        
        //$locale = $request->lang;

        $request = json_decode(file_get_contents("php://input"));
        $locale = isset($request->lang) && !empty($request->lang) ? $request->lang :"1";
        $api_key = isset($request->api_key) && !empty($request->api_key) ? $request->api_key :"";
        $screen_name = isset($request->screen_name) && !empty($request->screen_name) ? $request->screen_name :"";


        if(empty($api_key)){
            return response()->json(["status"=>201,"message"=>trans('messages.API Key comporsory')],201);
        }

        if($api_key!=env('API_KEY_auth')){
            return response()->json(["status"=>201,"message"=>trans('messages.API_KEY')],201);
        }
        if(empty($screen_name)){
            return response()->json(["status"=>201,"message"=>trans('messages.Screen Name comporsory')],201);
        }
        $hindi=DB::table('statics_text')->where('language_id', $locale)->where('screen_name', $screen_name)->get();
        if(count($hindi)>0)
        {
                $result['status'] = 200;
                $result['response'] = "Sucess";
                $result['data']=$hindi;
                return response()->json($result, 200);
        }
        else{
              $result['status'] = 201;
                $result['response'] = "N Data Found";
                
                return response()->json($result, 201);

        }
        
    }
}
