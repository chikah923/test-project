<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Post;
use App\Model\Comment as CommentModel;


class CommentsController extends Controller
{
   private $comment_model;

   public function __construct(CommentModel $comment_model)
   {
     $this->comment_model = $comment_model; 
   }
      

   public function store(Request $request, $id)
   {
     $this->validate($request, [
       'body'=>'required'
     ]);

     $input = $request->all();
   // dd($input);  //←body取得出来てるが、comments table更新にpostの他の情報も必要？
     $this->comment_model->createComment($input);
     return redirect()->action('PostsController@show', $id);
   }


    public function destroy( $comment)
     {
      //dd($comment);  //comments tableのidが入ってる
      $this->comment_model->deleteComment($comment);
      //dd($this);
      return redirect()->back();
     }

}


