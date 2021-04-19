<?php

namespace App\Http\Controllers\crm;

use App\Models\Database;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('crm.database.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showResults(Request $request)
    {
        $database = $request->input('database');

        $dbOneTables = DB::select('SHOW TABLES');
        $firstDatabasealltables = [];
        foreach ($dbOneTables as $key => $value) {
            $firstDatabasealltables[] = $value->Tables_in_banksathi;
        }
        $noOfTableDatabaseOne = count($firstDatabasealltables);

        DB::statement("USE $database");
        $dbTwoTables = DB::select('SHOW TABLES');

        // $dbTwoTables = DB::connection('mysql2')->select('SHOW TABLES');

        $key1 = "Tables_in_".$database;

        $secondDatabasealltables = [];
        foreach ($dbTwoTables as $key => $value) {
            $secondDatabasealltables[] = $value->$key1;
        }
 
        $noOfTableDatabaseTwo = count($secondDatabasealltables);


        $intersect = array_intersect($firstDatabasealltables, $secondDatabasealltables);
        $intersect = isset($intersect) ? $intersect : array();
        $diff = array_diff($firstDatabasealltables, $secondDatabasealltables);
        $diff = isset($diff) ? $diff : array();

        $cdata = array('intersect' => $intersect, 'diff' => $diff);

        return view('crm.database.results', compact('cdata','firstDatabasealltables','noOfTableDatabaseOne','secondDatabasealltables','noOfTableDatabaseTwo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Database  $database
     * @return \Illuminate\Http\Response
     */
    public function show(Database $database)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Database  $database
     * @return \Illuminate\Http\Response
     */
    public function edit(Database $database)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Database  $database
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Database $database)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Database  $database
     * @return \Illuminate\Http\Response
     */
    public function destroy(Database $database)
    {
        //
    }
}
