<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['image', 'post_id'];

    /** imagesテーブルとpostsテーブルの紐付きを設定
    *
    * @access public
    * @return void
    */
    public function post()
    {
        return $this->belongsTo('App\Model\Post');
    }

    /** カラム'post_id'がpostsテーブルのカラム'id'に等しいレコードの抽出
    *
    * @access public
    * @param  int $id
    * @return void
    */
    public function getImageOfPostId($id)
    {
        return $this->where('post_id', $id)
                    ->get();
    }

    /** imageの新規保存
    *
    * @access public
    * @param String[] $image
    * @return void
    */
    public function createImage($image)
    {
        return $this->create($image);
    }

    /** 該当するimageの削除
    *
    * @access public
    * @param  int $id
    * @return void
    */
    public function deleteImage($id)
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
    public function getImageFromId($id)
    {
        return $this->find($id);
    }

}

