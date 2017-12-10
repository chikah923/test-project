<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Post;

class PostsController extends Controller
{
    public function index() {
      $posts = Post::latest()->get();
      //dd($posts->toArray());
      return view ('posts.index')->with('posts', $posts);
    }
   
   public function store(Request $request) {
      $this->validate($request, [
         'body' => 'required|min:3',
      ]);

      $post = new Post();
      
      if (empty($post->name)) {
      $post->name = "名無しさん";
      } else {
      $post->name = $request->name;
      }
      $post->body = $request->body;
      $post->save();
      return redirect('/');
   }

   public function destroy(Post $post) {
      $post->delete();
      return redirect('/');
   }
 
   public function edit(Post $post) {
      return view('posts.edit')->with('post', $post);
   }

   public function update(Request $request, Post $post) {
      $this->validate($request, [
         'body' => 'required|min:3',
      ]);
    
      if (empty($post->name)) {
      $post->name = "名無しさん";
      } else {
      $post->name = $request->name;
      }

      $post->body = $request->body;
      $post->save();
      return redirect('/');
   }

}
