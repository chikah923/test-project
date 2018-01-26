<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Post;
use App\Model\Image as ImageModel;
use File;

class ImagesController extends Controller
{
    private $comment_model;

    /** commentモデルをインスタンス化する
    *
    * @access public
    * @param  obj $image_model
    * @return void
    */
    public function __construct(ImageModel $image_model)
    {
        $this->image_model = $image_model;
    }

    /** Imageの削除
    *
    * @access public
    * @param  obj
    * @return void
    */
    public function destroy($id)
    {
        // public/images フォルダにある画像を削除
        $image = $this->image_model->getImageFromID($id);
        File::delete(storage_path('app/public/prod/'.$image->session_id. '/'.$image->image));

        // 該当するレコードを削除
        $this->image_model->deleteImage($id);
        return redirect()->back();
    }

}

