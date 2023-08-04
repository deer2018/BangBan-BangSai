<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LineMessageAPI;
use Illuminate\Http\Request;

class LineWebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $template_path = storage_path('../public/flex-templates/places.json');
        $string_json = file_get_contents($template_path);
        $message = json_decode($string_json, true) ; 

        return $message;
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
        /*Get Data From POST Http Request*/
        // $datas = file_get_contents('php://input');
        // /*Decode Json From LINE Data Body*/
        // $deCode = json_decode($datas, true);
        
        //SAVE LOG
        file_put_contents('../storage/logs/log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
        
        $requestData = $request->all();

        $line = new LineMessageAPI();
        //TEXT
        $line->replyUser($requestData);
        
    }







    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}