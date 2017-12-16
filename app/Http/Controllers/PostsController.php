<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Model\Post as PostModel;
use Image;
use File;

class PostsController extends Controller
{
    private $post_model;

    /** Postモデルをインスタンス化する
    *
    * @access public
    * @param obj $post_model
    * @return void
    */
    public function __construct(PostModel $post_model)
    {
        $this->post_model = $post_model;
    }

    /** 既存のpostsデータを全件取得する
    *
    * @access public
    * @return response
    */
    public function index()
    {
        $posts = $this->post_model->getAllPost();
        return view ('posts.index')->with('posts', $posts);
    }

    /** postデータの新規保存
    *
    * @access public
    * @param  String[] $request
    * @return response
    */
    public function store(PostRequest $request)
    {
        /* RequestにFile uploadが含まれている場合、以下の処理をする */
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            // time stampを使ってFileをリネーム
            $filename = time() . '.' . $image->getClientOriginalExtension();
            // Fileの保存先を$loacationに格納
            $location = public_path('images/'. $filename);
            // Fileをリサイズして$locationに保存
            Image::make($image)->resize(200, 100)->save($location);
            // File名をpostsテーブルのカラムimageに保存
            $post = new PostModel;
            $post->image = $filename;
            // 配列にimageを追加
            $request['image'] = $filename;
        }
        /* $requestからパラメータの配列のみ取得し$inputに格納 */
        $input = $request->all();
        $this->post_model->createPost($input);
        return redirect('/');
    }

    /** 該当するpostデータの削除
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function destroy($id)
    {
        /* 該当するpostデータにFileが存在する場合、public/imagesディレクトリから画像を削除する */
        $post = $this->post_model->getPostFromId($id);
        if ($post->image!= NULL) {
            File::delete(public_path('images/'.$post->image));
        }
        $this->post_model->deletePost($id);
        return redirect('/');
    }

     /** 該当するpostからimageのみ削除
     *
     * @access public
     * @param  int $id
     * @return response
     */
     public function deleteImage($id)
     {
         // public/images フォルダにある画像を削除
         $post = $this->post_model->getPostFromId($id);
         File::delete(public_path('images/'.$post->image));
         // 該当するpostからimageのみNULLに更新
         $this->post_model->deleteImageFromPost($id);
         return redirect('/');
     }

    /** 該当するpostデータの編集画面に移動
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function edit($id)
    {
        $post = $this->post_model->getPostFromId($id);
        return view('posts.edit')->with('post', $post);
    }

    /** 該当するpostデータの更新
    *
    * @access public
    * @param  String[] $request
    * @return response
    */
    public function update(PostRequest $request)
    {
        /* RequestにFile uploadが含まれている場合、以下の処理をする */
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            // time stampを使ってFileをリネーム
            $filename = time() . '.' . $image->getClientOriginalExtension();
            // Fileの保存先を$loacationに格納
            $location = public_path('images/'. $filename);
            // Fileをリサイズして$locationに保存
            Image::make($image)->resize(400, 200)->save($location);
            // idから該当するpostレコードを取得する
            $post = $this->post_model->getPostFromId($request['id']);
            // 古いFile名を$oldFileNameに格納する
            $oldFileName= $post->image;
            // 新しいFile名をpostデータのimageカラムに格納する
            $post->image = $filename;
            // $requestの配列にimage = $filenameを追加
            $request['image'] = $filename;
            // public/imagesディレクトリから画像を削除する
            File::delete(public_path('images/'.$oldFileName));
            }

        /* $requestからパラメータの配列のみ取得し$inputに格納 */
        $input = $request->all();
        $this->post_model->updatePost($input);
        return redirect('/');
    }

    /** 該当するpostデータの表示
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function show($id)
    {
        $post = $this->post_model->showPost($id);
        return view('posts.show')->with('post', $post);
    }

}

