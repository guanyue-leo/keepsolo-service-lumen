<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Captcha extends Controller
{
    public function __construct(){
        session_start();
    }
    private function getIp()
    {

        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);

        return $cip;
    }

    public function img(Request $request){
        $width = $request->input('width');
        $height = $request->input('height');
        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocatealpha($img , 0 , 0 , 0 , 127);
        imagealphablending($img , false);
        imagefill($img , 0 , 0 , $bg);
        $color = imagecolorallocate($img,136,199,244);
        $x = mt_rand(30,$width);
        imagefilledellipse($img, $x, (int)($height*0.5), 6, 6, $color);
        imagesavealpha($img , true);
        $_SESSION['captcha']['key']  = (int)($x/$width*1000);
        $_SESSION['captcha']['time'] = time();
        header('Content-type:image/png');
        imagepng($img);
        imagedestroy($img);
    }

    public function verify(Request $request){
        $x    = $request->input('x');
        $time = $_SESSION['captcha']['time'];
        $key  = $_SESSION['captcha']['key'];

//        var_dump(session_id());
//        var_dump($_SESSION);
//        var_dump($_COOKIE);
//        var_dump(env('Access-Control-Allow-Origin', true));

        if(time() - $time > 3000)
        {
            return $this->error(-1,'','太久了，请再试一次');
        }
        if(abs($x - $key) > 100)
        {
            return $this->error(-1,'','请再试一次');
        }
        return $this->success('', '成功');
    }


}
