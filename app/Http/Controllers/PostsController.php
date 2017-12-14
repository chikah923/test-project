<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Model\Post as PostModel;

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
        /* $requestからパラメータの配列のみ取得し$inputに格納 */
        $input = $request->all();
        $this->post_model->createPost($input);
        return redirect('/');
    }

    /** 該当するpostデータの削除
    *
    * @access public
    * @param  int $id
    *         Postのidを格納
    * @return response
    */
    public function destroy($id)
    {
        $this->post_model->deletePost($id);
        return redirect('/');
    }

    /** 該当するpostデータの編集画面に移動
    *
    * @access public
    * @param  int $id
    *         Postのidを格納
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
    * @param  int $id
    *         Postのidを格納
    * @return response
    */
    public function update(PostRequest $request, $id)
    {
        $input = $request->all();
        $this->post_model->updatePost($input, $id);
        return redirect('/');
    }

    /** 該当するpostデータの表示
    *
    * @access public
    * @param  int $id
    *         Postのidを格納
    * @return response
    */
    public function show($id)
    {
        $post = $this->post_model->showPost($id);
        return view('posts.show')->with('post', $post);
    }

}

