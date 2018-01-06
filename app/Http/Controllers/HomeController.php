<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** 储存share到redis */
        $id=Auth::id();
        $share_key=Auth::user()->share;
        $key='share:'.$share_key."_".$id;
        $value=$share_key;
        Redis::set($key,$value);
        Redis::expire($key,3600);
        return view('home');
    }

    public function getContent($search='')
    {
        $info=[];
        $ssurl = hh_out((get_URL("http://zhannei.baidu.com/cse/search?q=" . $search . "&click=1&s=17194782488582577862&nsid=")));
        preg_match_all('/<a cpos="title" href="(.*?)" title="(.*?)" class="result-game-item-title-link" target="_blank">/', $ssurl, $tt);
        preg_match_all('/<span class="result-game-item-info-tag-title preBold">类型：<\/span> <span class="result-game-item-info-tag-title">(.*?)<\/span>/', $ssurl, $txtid);
        preg_match_all('/<span class="result-game-item-info-tag-title preBold">作者：<\/span> <span>(.*?)<\/span>/', $ssurl, $by);
        preg_replace('/utf-8/','gbk',$ssurl);
        $labels=$this->getLabel($ssurl);
        $articles=$this->getArticle($ssurl);
//        dump($articles);
        $info['label']=$labels;
        $info['books']=$articles;
        dump($info);
        return ;
    }

    /**
     * get Label
     * @param $content
     * @return array $allLabel
     */
    private function getLabel($content){
        preg_match_all('/class="result-filter-bd-row-list-item-link sc-game-select">(.*?)<\/a>/',$content,$label);
        $labels=$label[1];
        foreach ($labels as &$v){
            if($v=='0'){
                $v='全部';
            }
        }
        sort($labels);
        return $labels;
    }

    /**
     * get article with author,summary,img and link
     * @param string $content
     * @return array $article
     */
    private function getArticle($content){
        $result=[];
        //get img
        preg_match_all('/class="result-game-item-pic-link" target="_blank" style="width:110px;height:150px;">                <img src="(.*?)" alt="/',$content,$img);
        foreach ($img[1] as $key=>$value){
            $result[$key]['img']=$value;
        }
        //get link
        preg_match_all('/<a cpos="title" href="(.*?)" title/',$content,$link);
        foreach ($link[1]as $key=>$value){
            $result[$key]['link']=$value;
        }
        //get name
        preg_match_all("/class=\"result-game-item-title-link\" target=\"_blank\">                                    (.*?)                            <\/a> /",$content,$name);
        foreach ($name[1] as $key=>$value){
            $result[$key]['name']=$value;
        }
        //get Summary
        preg_match_all("/<p class=\"result-game-item-desc\">(.*?)<\/p>/",$content,$summary);
         foreach ($summary[1] as $key=>$value){
             $result[$key]['summary']=$value;
         }
        //get author


        return $result;

    }
}
