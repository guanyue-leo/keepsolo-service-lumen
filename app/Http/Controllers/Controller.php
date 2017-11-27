<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct(Request $request)
    {
        $this->tokenVerify($request->input('token'));
    }

    public function success($content = '', $message = '请求成功')
    {
        return response()->json(array('code' => 0, 'content' => $content, 'message' => $message));
    }

    public function error($code = -1, $content = '', $message = '请求失败')
    {
        return response()->json(array('code' => $code, 'content' => $content, 'message' => $message));
    }

    protected function encrypt($string,$operation,$key='')
    {

        $key=md5($key);

        $key_length=strlen($key);

        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;

        $string_length=strlen($string);

        $rndkey=$box=array();

        $result='';

        for($i=0;$i<=255;$i++){

            $rndkey[$i]=ord($key[$i%$key_length]);

            $box[$i]=$i;

        }

        for($j=$i=0;$i<256;$i++){

            $j=($j+$box[$i]+$rndkey[$i])%256;

            $tmp=$box[$i];

            $box[$i]=$box[$j];

            $box[$j]=$tmp;

        }

        for($a=$j=$i=0;$i<$string_length;$i++){

            $a=($a+1)%256;

            $j=($j+$box[$a])%256;

            $tmp=$box[$a];

            $box[$a]=$box[$j];

            $box[$j]=$tmp;

            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));

        }

        if($operation=='D'){

            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){

                return substr($result,8);

            }else{

                return'';

            }

        }else{

            return str_replace('=','',base64_encode($result));

        }

    }

    protected function tokenVerify($token){
        $key = 'www.keepsolo.com';
        $str = $this->encrypt($token, 'D', $key);
        if(!$str) exit(json_encode(array('code' => -1, 'content' => '', 'message' => 'token验证失败')));
        $value = explode('/',$str);
        $time  = $value[0];
        $id    = $value[1];
        if((time()-$time) > 36000) exit(json_encode(array('code' => -2, 'content' => '', 'message' => 'token过期')));
        $result = DB::table('user')->where('id', $id)->first();
        if(!$result) exit(json_encode(array('code' => -1, 'content' => '', 'message' => 'token验证失败')));
        return $result;
    }
}
