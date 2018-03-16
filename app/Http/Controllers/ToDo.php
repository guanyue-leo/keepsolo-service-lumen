<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToDo extends Controller
{
    public function __construct(Request $request)
    {

    }
    public function getItem(Request $request){
        $result = app('db')->table('todo')->select()->where([
            ['id', '=', $request->input('id')],
            ['user_id', '=', $this->getUserId($request->input('token'))]
        ])->get();
        if (!$result->first()) return $this->success('');
        return $this->success($result->first());
    }
    public function getList(Request $request){
        if($request->input('status'))
        {
            $result = app('db')->table('todo')->select()->where([
                ['status', '=', $request->input('status')],
                ['user_id', '=', $this->getUserId($request->input('token'))]
            ])
                ->orderBy('orderkey','desc')
                ->get();
        }
        else
        {
            $result = app('db')->table('todo')->select()->where('user_id', '=', $this->getUserId($request->input('token')))->orderBy('id','desc')->get();
        }
        if (!$result->first()) return $this->success([]);
        return $this->success($result);
    }
    public function insert(Request $request){
        date_default_timezone_set('PRC');
        $time    = time();
        $dateNow = date("Y-m-d H:i:s",$time);
        $dateAlert = date("Y-m-d H:i:s",$time+10800);
        $result = app('db')->table('todo')->insertGetId(
            array('title' => $request->input('title'), 'status' => $request->input('status'), 'user_id' => $this->getUserId($request->input('token')), 'create_time' => $dateNow, 'alert_time' => $dateAlert)
        );
        $result2 = app('db')->table('todo')->where([
            ['id', '=', $result],
        ])->update(array('orderkey' => $result));
        return $this->success($result2);
    }
    public function updateOrderBy(Request $request){
//        app('db')->listen(function($query) {
//            $bindings = $query->bindings;
//            $sql = $query->sql;
//            foreach ($bindings as $replace){
//                $value = is_numeric($replace) ? $replace : "'".$replace."'";
//                $sql = preg_replace('/\?/', $value, $sql, 1);
//            }
//            dd($sql);
//        });
        if($request->input('action') === 'plus')
        {
            $result = app('db')->table('todo')->where([
                ['orderkey', '>=', $request->input('startorder')],
                ['orderkey', '<=', $request->input('endorder')],
                ['user_id', '=', $this->getUserId($request->input('token'))]
            ])->increment('orderkey');;
        }
        else
        {
            $result = app('db')->table('todo')->where([
                ['orderkey', '>=', $request->input('startorder')],
                ['orderkey', '<=', $request->input('endorder')],
                ['user_id', '=', $this->getUserId($request->input('token'))]
            ])->decrement('orderkey');
        }
        $result1 = app('db')->table('todo')->where([
            ['id', '=', $request->input('dragid')],
            ['user_id', '=', $this->getUserId($request->input('token'))]
        ])->update(array('orderkey' => $request->input('neworder')));
        return $this->success($result);
    }
    public function update(Request $request){
        date_default_timezone_set('PRC');
        $time    = time();
        $dateNow = date("Y-m-d H:i:s",$time);
        $dateAlert = date("Y-m-d H:i:s",$time+10800);
        $result = app('db')->table('todo')->where([
            ['id', '=', $request->input('id')],
            ['user_id', '=', $this->getUserId($request->input('token'))]
        ])->update(array('status' => $request->input('status')));
        return $this->success($result);
    }
//    public function delete(Request $request){
//        $results = app('db')->table('article')->delete($request->input('id'));
//        $results = app('db')->table('tags_article')->where('aid', '=', $request->input('id'))->delete();
//        return $results;
//    }
}
