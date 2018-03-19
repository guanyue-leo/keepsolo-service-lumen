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
        $key = 'www.jihuatong.com';
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

    protected function getUserId($token){
        $result = $this->tokenVerify($token);
        return $result->id;
    }

    protected function getIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";

        $ch  = curl_init();
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        // set url

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//返回而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);
        //echo output
        $output = json_decode($output);
        // close curl resource to free up system resources
        curl_close($ch);
        return ($output);
    }
}
