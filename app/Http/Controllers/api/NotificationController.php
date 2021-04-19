<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProjectResource;
use App\Models\notifications;

class NotificationController extends BaseController
{

    public function get_noti(Request $request){

        $notisCount = notifications::orderByDesc("updated_at")->where('created_at', '<=', date('Y-m-d H:i:s'))->count();
        $notis = notifications::orderByDesc("updated_at")->limit(10)->where('created_at', '<=', date('Y-m-d H:i:s'))->get(["id","title",'content','updated_at']);
        // $notis = notifications::orderByDesc("updated_at")->limit(10)->get(["id","title",'content','updated_at']);
        $data = [];
        
        foreach($notis as $noti){
            $data[] = [
                'id' => $noti->id,
                'title' => $noti->title,
                'content' => $noti->content,
                'updated_at' => $noti->updated_at
            ];
        }

        $res["current_time"] = date("Y-m-d H:i:s");
        $res["notification"] = $data;
        $res["count"] = $notisCount;
        return $this->sendResponse(new ProjectResource($res), "Performed successfully");

    }

}
