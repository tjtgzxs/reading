<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BooksController extends Controller
{
    //
    function getCatalogue($c1,$c2){
        $t=file_get_contents("http://www.bqg5200.com/xiaoshuo/{$c1}/{$c2}");
        $content=iconv("gb2312", "utf-8//IGNORE",$t);
       // preg_match_all("/<li class=\"fj\"><h3>正文<\/h3><div class=\"border-line\"><\/div> <\/li>(.+)<div class=\"all_ad clearfix mtop\" id=\"ad_980_2\"><\/div>/",$content,$list);
        $content=strstr($content,"<li class=\"fj\"><h3>正文</h3><div class=\"border-line\"></div> </li>");
        $content=strstr($content,'</ul>',true);
        dump($content);
    }
}
