<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;

class Post extends Model
{
    protected $fillable = ['name', 'body', 'image'];

    /** commentsテーブルとpostsテーブルの紐付きを設定
    *
    * @access public
    * @return void
    */
    public function comments()
    {
        return $this->hasMany('App\Model\Comment');
    }

    /** imagesテーブルとpostsテーブルの紐付きを設定
    *
    * @access public
    * @return voild
    */
    public function images()
    {
        return $this->hasMany('App\Model\Image');
    }

    /** postsテーブルのレコードを全件取得
    *
    * @access public
    * @return void
    */
    public function getAllPost()
    {
        return $this->orderBy('created_at', 'desc')
                    ->paginate(10);
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

    /** 該当するpostからimageのみNULLに更新
    *
    * @access public
    * @param  int $id
    * @return void
    */
    public function deleteImageFromPost($id)
    {
         DB::table('posts')->where('id', $id)
                           ->update(['image' => NULL]);
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
    * @return void
    */
    public function updatePost($input)
    {
  // dd($input);
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

