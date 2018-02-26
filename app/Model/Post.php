<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['name', 'body', 'image', 'chk_flg'];

    /**
     * commentsテーブルとpostsテーブルの紐付きを設定
     *
     * @access public
     * @return void
     */
    public function comments()
    {
        return $this->hasMany('App\Model\Comment');
    }

    /**
     * imagesテーブルとpostsテーブルの紐付きを設定
     *
     * @access public
     * @return voild
     */
    public function images()
    {
        return $this->hasMany('App\Model\Image');
    }

    /**
     * tagsテーブルとpostsテーブルの紐付きを設定
     *
     * @access public
     * @return void
     */
    public function tags()
    {
        return $this->belongsToMany('App\Model\Tag');
    }

    /**
     * カラムchk_flgがtrueのレコードを全件取得(作成日時の降順)
     *
     * @access public
     * @return void
     */
    public function getAllAuthedPost()
    {
        return $this->where('chk_flg', true)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
    }

    /**
     * カラムchk_flgがfalseのレコードを全件取得(作成の降順)
     *
     * @access public
     * @return void
     */
    public function getAllPostNeedAuth()
    {
        return $this->where('chk_flg', false)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
    }

    /**
     * レコード全件取得(更新日時の降順)
     *
     * @access public
     * @return void
     */
    public function getAllPostByLastUpdated()
    {
        return $this->orderby('updated_at', 'desc')
                    ->paginate(10);
    }

    /**
     * レコードの新規保存
     *
     * @access public
     * @param string[] $input
     * @return void
     */
    public function createPost(array $input)
    {
        return $this->create($input);
    }

    /**
     * 該当するレコードの削除
     *
     * @access public
     * @param int $id
     * @return void
     */
    public function deletePost(int $id)
    {
        return $this->findOrFail($id)
                    ->delete();
    }

    /**
     * 該当するレコードの取得
     *
     * @access public
     * @param int $id
     * @return void
     */
    public function getPostFromId(int $id)
    {
        return $this->findOrFail($id);
    }

    /**
     * 該当するレコードの更新
     *
     * @access public
     * @param string[] $input
     * @return void
     */
    public function updatePost(array $input)
    {
        return $this->findOrFail($input['id'])
                    ->update($input);
    }

    /**
     * 該当するレコードを取得
     *
     * @access public
     * @param int $id
     * @return void
     */
    public function showPost(int $id)
    {
        return $this->findOrFail($id);
    }


    /**
     * 中間テーブルtag_postにレコードを挿入
     *
     * @access public
     * @param obj $post
     * @param int[] $tag
     * @return void
     */
    public function createTagPost(object $post, array $tag){
        return $post->tags()->attach($tag);
    }

    /**
     * カラムchk_flgの値をtrueに更新
     *
     * @access public
     * @param int $id
     * @return void
     */
    public function UpdateColumnChkFlg(int $id)
    {
        return $this->where('id', $id)
                    ->update(['chk_flg' => true]);
    }

}
