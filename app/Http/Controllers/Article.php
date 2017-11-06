<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Article extends BaseController
{
    public function getItem(Request $request){
        $result = app('db')->table('article')->select()->where('id', '=', $request->input('id'))->get();
        $results = app('db')->table('tags_article')->select()->where('aid', '=', $request->input('id'))->get();
        if (!$result->first()) {
            return false;
        }
        $re = (object)[];
        $re->info = $result->first();
        $re->tags = $results;

        return json_encode($re);
    }
    public function getList(Request $request){
        $tagstring = '';
        $tags = $request->input('tags');
        for($i=0;$i < count($tags);$i++){
            $tagstring .= $i==(count($tags)-1)?"'".$tags[$i]."'":"'".$tags[$i]."'".',';
        }
        $start = $request->input('pageSize') * ($request->input('pageNum')-1);
        if($tags !== '全部'){
            $txt = <<<EOT
SELECT tb.aid AS id,tb.title AS title,tb.date AS date,tb.content AS content
FROM
(
    (
        SELECT
            article.id AS aid,article.title AS title,article.date AS date,article.content AS content,tags_article.tid AS tid
        FROM
            article
        LEFT  JOIN tags_article
        on
            article.id = tags_article.aid
    ) AS tb
    LEFT JOIN tags
    on
        tb.tid = tags.id  
)  WHERE tags.`name` in 
EOT;

            $results = app('db')->select("{$txt}({$tagstring}) GROUP BY tb.aid LIMIT {$start},{$request->input('pageSize')}");
        }else {
            $results = app('db')->select("SELECT * FROM article LIMIT {$start},{$request->input('pageSize')}");
        }
        return $results;
    }
    public function insert(Request $request){
        $results = app('db')->table('article')->insertGetId(
            array('title' => $request->input('title'), 'content' => $request->input('content'))
        );
        $tags = $request->input('tags');
        for($i=0;$i < count($tags);$i++){
            app('db')->insert('insert into tags_article (aid,tid) values (?, ?)', [$results, $tags[$i]]);
        }
        return $results;
    }
    public function delete(Request $request){
        $results = app('db')->table('article')->delete($request->input('id'));
        $results = app('db')->table('tags_article')->where('aid', '=', $request->input('id'))->delete();
        return $results;
    }
}
