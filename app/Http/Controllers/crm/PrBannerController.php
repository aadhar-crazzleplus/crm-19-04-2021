<?php

namespace App\Http\Controllers\crm;

use App\Http\Controllers\Controller;
use App\Models\PrBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PrBannerController extends Controller
{
    public function __construct(Request $request) {
        $this->middleware(function ($request, $next){
            $sessionArray = Session::get('sessionArray');
            $socialBannerModule = [];
            
            foreach ($sessionArray['permissions'] as $key => $value) {
                if ($value['module']=='socialBanner') {
                    $socialBannerModule = explode(",", $value['permissions']);
                }
            }
    
            $this->socialBannerModule = $socialBannerModule;

            if (in_array(1, $socialBannerModule)) {
                return $next($request);
            } else {
                return redirect("login")->with("error","Oops!! you don't have access to this module...");
            }
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['banners'] = PrBanner::where('status','1')->get();
        return view('crm.pr_banners.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!in_array(3, $this->socialBannerModule)) {
            abort(401, 'Unauthorized access.');
        }

        return view('crm.pr_banners.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!in_array(3, $this->socialBannerModule)) {
            abort(401, 'Unauthorized access.');
        }

        $this->validate($request, [
            'title' => 'required',
            'banner_img' => 'required|image|max:2048',
        ]);

        $fileNameToStore='';
        $banner_img = "";
        $year = date("Y");
        $month = date("M");
        $path = 'public/pr_banners/'.$year.'/'.$month.'/';
        $publicPath = 'pr_banners/'.$year.'/'.$month.'/';
        if($request->hasFile('banner_img')){
            // file upload
            $file = $request->file('banner_img');
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

            $banner_img = time().$banner_img;
            $request->file('banner_img')->storeAs($path, $fileNameToStore);

            // Insert Banner Details
            $banner = new PrBanner;
            $banner->title = $request->input('title');
            $banner->image = $publicPath.$fileNameToStore;
            $banner->save();
        }

        return redirect()->route('crm-pr-banners')->with('success', 'Banner created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrBanner  $prBanner
     * @return \Illuminate\Http\Response
     */
    public function show(PrBanner $prBanner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PrBanner  $prBanner
     * @return \Illuminate\Http\Response
     */
    public function edit(PrBanner $prBanner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PrBanner  $prBanner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrBanner $prBanner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PrBanner  $prBanner
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrBanner $prBanner)
    {
        //
    }

    public function sanitizeString($string='')
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
