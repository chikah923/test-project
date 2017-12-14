<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['body', 'post_id'];

   /** commentsテーブルとpostsテーブルの紐付きを設定
    *
    * @access public
    * @return void
    */
    public function post()
    {
        return $this->belongsTo('App\Model\Post');
    }

    /** commentの新規保存
    *
    * @access public
    * @param String[] $input
    * @return void
    */
    public function createComment($input)
    {
        return $this->create($input);
    }

    /** 該当するcommentの削除
    *
    * @access public
    * @param  int $comment
    *         削除対象Commentのidを格納
    * @return void
    */
    public function deleteComment($comment)
    {
        return $this->find($comment)
                    ->delete();
    }
}

