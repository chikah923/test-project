<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['body', 'post_id'];

    /**
     * commentsテーブルとpostsテーブルの紐付きを設定
     *
     * @access public
     * @return void
     */
    public function post()
    {
        return $this->belongsTo('App\Model\Post');
    }

    /**
     * commentの新規保存
     *
     * @access public
     * @param string[] $input
     * @return void
     */
    public function createComment(array $input)
    {
        return $this->create($input);
    }

    /**
     * 該当するcommentの削除
     *
     * @access public
     * @param int $id
     * @return void
     */
    public function deleteComment(int $id)
    {
        return $this->findOrFail($id)
                    ->delete();
    }
}

