<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Post as PostModel;

class Tag extends Model
{
    protected $fillable = ['name'];


    /** tagsテーブルとpostsテーブルの紐付きを設定
    *
    * @access public
    * @return void
    */
    public function posts()
    {
        return $this->belongsToMany('App\Model\Post');
    }

    /** レコードを全件取得
    *
    * @access public
    * @return void
    */
    public function getAllTag()
    {
        return $this->all();
    }

}

