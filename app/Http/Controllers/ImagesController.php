<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Post;
use App\Model\Image as ImageModel;
use File;
use DB;

class ImagesController extends Controller
{
    private $comment_model;

    /** Imageモデルをインスタンス化する
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
    * @return response
    */
    public function destroy(int $id)
    {
        // public/images フォルダにある画像を削除
        $image = $this->image_model->getImageFromID($id);
        try {
            DB::transaction(function () use ($image, $id) {
                File::delete(storage_path('app/public/prod/'.$image->session_id. '/'.$image->image));
                // 該当するレコードをテーブルから削除
                $this->image_model->deleteImage($id);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return redirect()->back();
    }
}

