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

    /** tagsテーブルとpostsテーブルの紐付きを設定
    *
    * @access public
    * @return void
    */
    public function tags()
    {
        return $this->belongsToMany('App\Model\Tag');
    }

    /** postsテーブルのレコードを全件取得(作成日時の降順)
    *
    * @access public
    * @return void
    */
    public function getAllPost()
    {
        return $this->orderBy('created_at', 'desc')
                    ->paginate(10);
    }

    /** postsテーブルのレコードを全件取得(更新日時の降順)
    *
    * @access public
    * @return void
    */
    public function getAllPostByLastUpdated()
    {
        return $this->orderby('updated_at', 'desc')
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
        return $this->findOrFail($id)
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
        return $this->findOrFail($id);
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
        return $this->findOrFail($input['id'])
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
        return $this->findOrFail($id);
    }


    /** 中間テーブルtag_postにレコードを挿入する
    *
    * @access public
    * @param  String[] $post
    * @param  int $tag
    * @return void
    */
    public function createTagPost($post, $tag){
        return $post->tags()->attach($tag);
    }

}
