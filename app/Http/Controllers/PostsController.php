<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Model\Post as PostModel;

class PostsController extends Controller
{
   private $post_model;

   public function __construct(PostModel $post_model)
   {
     $this->post_model = $post_model;
   }


   public function index() 
   {
      $posts = $this->post_model->getAllPost();
      return view ('posts.index')->with('posts', $posts);
   }
   

   public function store(PostRequest $request)
   {
      $input = $request->all();
      $this->post_model->createPost($input);
      return redirect('/');
   }


   public function destroy($id)
   {
      $this->post_model->deletePost($id);
      return redirect('/');
   }
 

   public function edit($id) {
      $post = $this->post_model->getPostFromId($id);
      return view('posts.edit')->with('post', $post);
   }

   public function update(PostRequest $request, int $id)
   {
      $input = $request->all();
      //dd($input); $requestの中からtable更新に必要な$inputを取得出来てるか確認
      $this->post_model->updatePost($input, $id);
      return redirect('/');
   }

}
