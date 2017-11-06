<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Tag extends BaseController
{
    public function getList(Request $request){
        $result = app('db')->table('tags')->select()->get();
        return $result;
    }
    public function insert(Request $request){
        $results = app('db')->table('tags')->insertGetId(
            array('name' => $request->input('name'))
        );
        return $results;
    }
    public function delete(Request $request){
        $results = app('db')->table('tags')->delete($request->input('id'));
//        $results = app('db')->table('tags_article')->where('tid', '=', $request->input('id'))->delete();
        return $results;
    }
}
