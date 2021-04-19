<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use App\Models\social_cards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProjectResource;
use App\Models\social_card_cats;
use App\Models\social_card_rels;

class SocialCardController extends BaseController
{
    public function social_view(){
        $data['categories'] = social_card_cats::where("status",'1')->get(["title","id"]);
        return view("crm.social_view",$data);
    }
    public function social_add(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'image' => 'required',
            'cat_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $path = 'public/social_banners/'.$request->cat_id;
        $image = "";
        if($request->hasFile('image')){
            $image = substr(str_replace(" ","-",$request->file('image')->getClientOriginalName()),-10);
            $image = time().$image;
            $request->file('image')->storeAs($path, $image);
        }
        $user = auth("admin")->user();
        $cards["title"] = $request->title;
        $cards["image"] = $image;
        $cards["updated_by"] = $user->id;
        $cards["status"] = "1";
        $card = social_cards::create($cards);
        $cat["social_cat_id"] = $request->cat_id;
        $cat["social_card_id"] = $card->id;
        $cat_rel = social_card_rels::create($cat);
        $data["status"] = "Ok";
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
    public function social_edit($id){
        $card = social_cards::find($id);
        $data["id"] = $card->id;
        $data["cat_id"] = $card->cat_rel->social_cat_id;
        $data["cat_rel_id"] = $card->cat_rel->id;
        $data["title"] = $card->title;
        $data["status"] = $card->status;
        $data["image"] = ($card->image != "")?url("storage/social_banners/".$card->cat_rel->social_cat_id."/".$card->image):"";

        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
    public function social_update(Request $request){
        $card = social_cards::find($request->id);
        // $data["cat_id"] = $card->cat_rel->social_cat_id;
        $path = 'public/social_banners/'.$request->cat_id;
        $image = "";
        if($request->hasFile('image')){
            $image = substr(str_replace(" ","-",$request->file('image')->getClientOriginalName()),-10);
            $image = time().$image;
            $request->file('image')->storeAs($path, $image);
        }elseif($request->social_id != $request->id){
            return $this->sendError('Image Error.', array("err_msg"=>["Please upload Image !!"]));
        }
        $user = auth("admin")->user();
        $card->title = $request->title;
        if($image != "")
        $card->image = $image;
        $card->updated_by = $user->id;
        $card->status = $request->status;
        $card->update();

        $cat_rel = social_card_rels::find($request->cat_rel_id);
        $cat_rel->social_card_id = $request->id;
        $cat_rel->social_cat_id = $request->cat_id;
        $cat_rel->update();

        $data["status"] = "Ok";
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
    public function social_show(Request $request){
        $card = social_cards::find($request->id);
        if($card->status == "1"){
            $so_status = "<span class='text-success'>Active</span>";
        }else{
            $so_status = "<span class='text-danger'>InActive</span>";
        }
        $data["id"] = $card->id;
        $data["cat_title"] = $card->cat_rel->getCat->title;
        $data["title"] = $card->title;
        $data["updated_by"] = $card->updatedBy->first_name." ".$card->updatedBy->last_name;
        $data["status"] = $so_status;
        $data["image"] = ($card->image != "")?url("storage/social_banners/".$card->cat_rel->social_cat_id."/".$card->image):"";
        return $this->sendResponse(new ProjectResource($data), "Performed successfully");
    }
    public function social_delete(){
        return view("crm.social_view");
    }
    public function social_list(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $active = array("act","acti","activ","active");
        $inactive = array("inac","inact","inacti","inactiv","inactive");
        if(in_array($searchValue, $active)){
            $searchValue = "1";
        }
        if(in_array($searchValue, $inactive)){
            $searchValue = "0";
        }
        $user = auth("admin")->user();

        // Total records
        $totalRecords = social_cards::select('count(*) as allcount')
                            ->count();

       $totalRecordswithFilter = social_cards::select('count(*) as allcount')
                            ->where(function($query) use ($searchValue){
                                $query->where('status', 'LIKE', '%'.$searchValue.'%')
                                    ->orWhereIn('updated_by',function($veh_query) use ($searchValue){
                                        $veh_query->select('id')->from('admins')
                                        ->where('first_name', 'LIKE', '%'.$searchValue.'%')
                                        ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%');
                                     })
                                    ->orWhere('title', 'LIKE', '%'.$searchValue.'%');
                            })->count();
        // Fetch records
        $records = social_cards::orderBy($columnName,$columnSortOrder)
                    ->where(function($query) use ($searchValue){
                        $query->where('status', 'LIKE', '%'.$searchValue.'%')
                            ->orWhereIn('updated_by',function($veh_query) use ($searchValue){
                                $veh_query->select('id')->from('admins')
                                ->where('first_name', 'LIKE', '%'.$searchValue.'%')
                                ->orWhere('last_name', 'LIKE', '%'.$searchValue.'%');
                            })
                            ->orWhere('title', 'LIKE', '%'.$searchValue.'%');
                    })
                    // ->select('users.*')
                    ->skip($start)
                    ->take($rowperpage)
                    ->get();

        $lead_arr=array();
        if(!empty($records))
        foreach($records as $record){
            if($record->status == "1"){
                $so_status = "<span class='text-success'>Active</span>";
                $updated_at= $record->updated_at;
            }else{
                $so_status = "<span class='text-danger'>InActive</span>";
                $updated_at= $record->updated_at;
            }

            $edit = route('social-edit',$record->id);
            $delete = route('social-delete',$record->id);
            $action = '<a data-toggle="modal" data-target="#editModal1" href="#editModal1" onclick="editSocialCard(\''.$edit.'\');">Edit</a> | <a href="javascript:;" onclick="deleete(\''.$delete.'\');">Delete</a>';

            $lead_arr[]=[
                // "lead_id"=>$lead->id,
                "title"=>'<a data-toggle="modal" data-target="#showModal1" href="#showModal1" onclick="showSocialCard(\''.$record->id.'\');">'.$record->title.'</a>',
                "status"=>$so_status,
                "updated_by"=>$record->updatedBy->first_name." ".$record->updatedBy->last_name,
                "updated_at"=>date("d-m-Y H:i:s",strtotime($updated_at)),
                "action" => $action,
            ];
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $lead_arr
        );

        return response()->json($response);
    }
}
