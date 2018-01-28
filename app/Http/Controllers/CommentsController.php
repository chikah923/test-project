<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Post;
use App\Model\Comment as CommentModel;
use App\Http\Requests\CommentRequest;

class CommentsController extends Controller
{
    private $comment_model;

    /** commentモデルをインスタンス化する
    *
    * @access public
    * @param  obj $comment_model
    * @return void
    */
    public function __construct(CommentModel $comment_model)
    {
        $this->comment_model = $comment_model;
    }

    /**  commentの新規保存
    *
    * @access public
    * @param  String[] $request
    * @return response
    */
    public function store(CommentRequest $request)
    {
        /* $requestからパラメータのみ取得し$inputに格納 */
        $input = $request->all();
        $this->comment_model->createComment($input);
        return redirect('posts/show/'.$input['post_id']);
    }

    /** 該当するcommentの削除
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function destroy($id)
    {
        $this->comment_model->deleteComment($id);
        return redirect()->back();
    }

}

