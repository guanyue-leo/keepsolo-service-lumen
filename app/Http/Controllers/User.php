<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class User extends Controller
{
    public function __construct(Request $request)
    {
        session_start();
    }
    public function login(Request $request)
    {
        $key = 'www.jihuatong.com';
        $time = time();

        $userName = $request->input('userName');
        $passWord = $request->input('passWord');
        if(!$userName || !$passWord) return $this->error(-1,'','请填写正确的信息');
        $result = DB::table('user')->where('name', $userName)->where('password', $passWord)->first();
        if (!$result) return $this->error(-1,'','密码错误');
        $result->token = $this->encrypt($time.'/'.$result->id, 'E', $key);
        unset($result->password);
        unset($result->id);
        if ($userName === 'guest')
        {
            date_default_timezone_set('PRC');
            $time    = time();
            $dateNow = date("Y-m-d H:i:s",$time);
            $ip = $this->getIp();
            $logResult = app('db')->table('loginlog')->insertGetId(
                array('time' => $dateNow, 'ip' => $ip->data->ip, 'position' => $ip->data->country.'-'.$ip->data->area.'-'.$ip->data->region.'-'.$ip->data->city)
            );
        }
        return $this->success($result);
    }
}
