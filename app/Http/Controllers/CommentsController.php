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
        $this->comment_model->createComment($input);
        return redirect()->action('PostsController@show', $id); // routingを通るように書き換えが必要
    }

    public function destroy($comment)
    {
        $this->comment_model->deleteComment($comment);
        return redirect()->back();
    }

}

