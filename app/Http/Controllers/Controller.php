<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function success($content = '', $message = '请求成功')
    {
        return response()->json(array('code' => 0, 'content' => $content, 'message' => $message));
    }

    public function error($code = -1, $content = '', $message = '请求失败')
    {
        return response()->json(array('code' => $code, 'content' => $content, 'message' => $message));
    }
}
