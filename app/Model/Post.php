<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['name', 'body'];

    /** commentsテーブルとpostsテーブルの紐付きを設定
    *
    * @access public
    * @return void
    */
    public function comments()
    {
        return $this->hasMany('App\Model\Comment');
    }

    /** postsテーブルのレコードを全件取得
    *
    * @access public
    * @return void
    */
    public function getAllPost()
    {
        return $this->orderBy('created_at', 'desc')
                    ->get();
    }

    /** postの新規保存
    *
    * @access public
    * @param  String[] $input
    * @return void
    */
    public function createPost($input)
    {
        return $this->create($input);
    }

    /** 該当するpostの削除
    *
    * @access public
    * @param  int $id
    * @return void
    */
    public function deletePost($id)
    {
        return $this->find($id)
                    ->delete();
    }

    /** 該当するpostレコードの取得
    *
    * @access public
    * @param  int $id
    * @return void
    */
    public function getPostFromId($id)
    {
        return $this->find($id);
    }

    /** 該当するpostの更新
    *
    * @access public
    * @param  String[] $input
    * @param  int $id
    * @return void
    */
    public function updatePost($input)
    {
        return $this->find($input['id'])
                    ->update($input);
    }

    /** 該当するpostのレコードを取得
    *
    * @access public
    * @param  int $id
    * @return void
    */
    public function showPost($id)
    {
        return $this->find($id);
    }

}

