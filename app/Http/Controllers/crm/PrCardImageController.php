<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PrCardImage;
use App\Models\PrCardCategory;
use App\Models\PrCardCategoryPrCardImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrCardImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['prCardCategory'] = PrCardCategory::where("status",'1')->with('cardimages')->get(["name","id"]);
        $data['prCardImage'] = PrCardImage::where("status",'1')->with('pivotCategory')->get(["title","name","id"]);
        return view('crm.pr_cards.index', $data);
        // return view('crm.pr_cards.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['prCardCategory'] = PrCardCategory::where("status",'1')->get(["name","id"]);
        return view('crm.pr_cards.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'card_img' => 'required|image|max:2048',
            'categories' => 'required'
        ]);

        $fileNameToStore='';
        $card_img = "";
        $year = date("Y");
        $month = date("M");
        $path = 'public/pr_cards/'.$year.'/'.$month.'/';
        $publicPath = 'pr_cards/'.$year.'/'.$month.'/';
        if($request->hasFile('card_img')){
            // $card_img = substr(str_replace(" ","-",$request->file('card_img')->getClientOriginalName()),-10);

            // file upload
            $file = $request->file('card_img');
            // Get filename with extension.
            $fileNameWithExt = $file->getClientOriginalName();

            // Get just the filename.
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

            // Sanitizing Filename.
            $filename = filter_var($filename, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
            $filename = $this->sanitizeString($filename);

            // Get the file extension.
            $extension = $file->getClientOriginalExtension();
            // Create new filename.
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $card_img = time().$card_img;
            $request->file('card_img')->storeAs($path, $fileNameToStore);

            // Insert path
            $photo = new PrCardImage;
            $photo->title = $request->input('title');
            $photo->name = $publicPath.$fileNameToStore;
            $photo->save();
            $photo_id = $photo->id;
            
            // PrCardCategory
            foreach ($request->input('categories') as $key => $id) {
                $photo = new PrCardCategoryPrCardImage;
                $photo->pr_card_image_id = $photo_id;
                $photo->pr_card_category_id = $id;
                $photo->save();
            }
        }


        return redirect()->route('pr-card')->with('success', 'Card created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrCardImage  $prCardImage
     * @return \Illuminate\Http\Response
     */
    public function show(PrCardImage $prCardImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PrCardImage  $prCardImage
     * @return \Illuminate\Http\Response
     */
    public function edit(PrCardImage $prCardImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PrCardImage  $prCardImage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrCardImage $prCardImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PrCardImage  $prCardImage
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $card = PrCardImage::find($id);
        $card->status = '0';
        $card->save();
        return redirect()->route('crm-pr-cards')->with('success', 'Card deleted successfully.');
    }


    public function getCards(Request $request)
    {
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

        $user = auth("admin")->user();

        // Total records
        $records=array();
        $totalRecords = PrCardImage::select('count(*) as allcount')
                        ->where("status",'>','0')->count();

        $totalRecordswithFilter = PrCardImage::select('count(*) as allcount')
                        ->where(function($query) use ($searchValue){
                            $query->where('id', 'LIKE', '%'.$searchValue.'%');
                        })
                        ->where("status",'>','0')->count();

        // Fetch records
        $records = PrCardImage::orderBy($columnName,$columnSortOrder)
            ->where(function($query) use ($searchValue){
                $query->where('id', 'LIKE', '%'.$searchValue.'%');
            })
            ->where("status",'>','0')
            ->skip($start)
            ->with('pivotCategory.associatedCategories')
            ->take($rowperpage)
            ->orderBy('id','ASC')
            ->get();

        /* 
        $category = [];
        foreach ($records as $key => $value) {
            $category[$value->id] = $value->pivotCategory->pluck('pr_card_category_id');
            // $category[$value->id] = $value->pivotCategory->pluck('associatedCategories');
        }

        $imageCategory = [];
        foreach ($category as $ky => $val) {
            $imageCategory[$ky] = $val;
        }

        $imgCat = json_decode(json_encode($category), true);
         */
    

        $data_arr = array();
        if(!empty($records))
        foreach($records as $record){

            if ($record->status==1) {
                $status = "<span class='notice bg-success' style='color:#ffffff;'>Active</span>";
            } else {
                $status = "<span class='notice bg-danger' style='color:#ffffff;'>Deactive</span>";
            }

            $id = $record->id;
            
            $name = "<img style='height:80px; width: 80px;' class='img img-thumbnail' src='".asset('storage/'.$record->name)."' alt='".$record->id."'>";
            
            $name = $name;
     
            $created_at = date("d-m-Y:H:i:s",strtotime($record->created_at));

            $delete = route('crm.delete-card',$record->id);
            $action = '<a class="btn btn-danger btn-xs" href="javascript:;" onclick="deleete(\''.$delete.'\');">Delete</a>';

            $data_arr[] = array(
                "id" => $id,
                "name" => $name,
                "status" => $status,
                "created_at" => $created_at,
                "action" => $action,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }


    public function sanitizeString($string='')
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }


}
