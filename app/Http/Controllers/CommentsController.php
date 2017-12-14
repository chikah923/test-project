<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Post;
use App\Model\Comment as CommentModel;

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
    *
    * @param  int $id
    *             commentに紐づくpostのidを格納
    * @return response
    * @todo   routingを通るように書き換えが必要
    */
    public function store(Request $request, $id)
    {
        $this->validate($request, [
        'body'=>'required'
         ]);
        /* $requestからパラメータのみ取得し$inputに格納 */
        $input = $request->all();
        $this->comment_model->createComment($input);
        return redirect()->action('PostsController@show', $id);
    }

    /** 該当するcommentの削除
    *
    * @access public
    * @param  int $comment
    *             削除対象Commentのidを格納
    * @return response
    */
    public function destroy($comment)
    {
        $this->comment_model->deleteComment($comment);
        return redirect()->back();
    }

}

