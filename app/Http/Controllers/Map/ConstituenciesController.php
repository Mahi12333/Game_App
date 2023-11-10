<?php

namespace App\Http\Controllers\Map;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ConstituenciesController extends Controller
{
    public function search()
    {
        try {
            $request = json_decode(file_get_contents("php://input"));
                             
                    if (empty($request->keyword)) {
                        return response()->json(["status" => 201, "message" => trans('messages.Keyword is comporsory')], 201);
                    }
                    $keyword = $request->keyword;

                    if (!empty($keyword)) {
                                               
                        $constituencies = DB::table('constituencies_list')
                        ->select('id', 'constituencies_name', 'state', 'lat', 'longt')
                        ->where('constituencies_name', 'like', '%' . $keyword . '%')
                        ->orderBy('constituencies_name', 'asc')
                        ->limit(5)
                        ->get();

                            $result['status']=200;
                            $result['responce']='Constituecies list';
                            $result['data']=$constituencies;
                            return response()->json($result, 200);
                    } else {
                        return response()->json(["status" => 400, "message" => "Keyword is required"]);
                    }
            
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
