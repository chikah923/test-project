<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Model\Post;
use App\Http\Requests\PostRequest;
use App\Model\Image as ImageModel;
use Image;
use File;
use Session;
use DB;
use Validator;

class ImagesController extends Controller
{
    private $comment_model;

    /**
     * ImageModelのインスタンスを作成する
     *
     * @access public
     * @param  obj $image_model
     * @return void
     */
    public function __construct(ImageModel $image_model)
    {
        $this->image_model = $image_model;
    }

    /**
     * Imageの個別削除
     *
     * @access public
     * @param int $id
     * @return response //edit画面にリダイレクトする
     */
    public function destroyImage(int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $images = $this->image_model->getImageFromID($id);
                $this->deleteImageFromPublic($images);
                $this->image_model->deleteImage($id);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return redirect()->back();
    }

    /**
     * public/storages/prodディレクトリから画像を削除
     *
     * @access public
     * @param obj $images
     * @return void
     */
    public function deleteImageFromPublic(object $images)
    {
        foreach ($images as $image) {
            File::delete(storage_path('app/public/prod/'.$image->session_id. '/'.$image->image));
        }
    }

    /**
     * public/storages/tempディレクトリに画像を保存
     *
     * @access public
     * @param obj $request
     * @return \ArrayObject<string>
     */
    public function confirmImage(PostRequest $request)
    {
        $images = $request->file('featured_image');
        $imageArray = [];
        foreach ($images as $file) {
            $fileName = $file->getClientOriginalName();
            $sessionId = Session::getId();
            // Session IDの名前で保存先ディレクトリを作成
            $path = storage_path('app/public/temp/'.$sessionId);
            if(file_exists($path)){
            } else {
                mkdir($path, '0777');
            }
            // Fileの保存先を$loacationに格納
            $location = ($path.'/'. $fileName);
            // Viewに渡すFileパスを$filePathに格納
            $filePath = ($sessionId.'/'. $fileName);
            // Fileをリサイズして$locationに保存
            Image::make($file)->resize(200, 100)->save($location);
            array_push($imageArray, $filePath);
            $this->deleteDelectoryInTime($location, $path);
            }
        return $imageArray;
    }

    /**
     * 2時間後にfileとSeddion Id名のディレクトリを削除する
     *
     * @access private
     * @param string $location
     * @param string $path
     */
    private function deleteDelectoryInTime($location, $path)
    {
        $expire = strtotime("2 hours ago");
        if(filemtime($location)<$expire) {
            unlink($location);
            rmdir($path);
        }
    }


    /**
     * 画像をpublic/prodディレクトリに保存し画像名をimagesテーブルに保存する
     *
     * @access public
     * @param obj $post
     * @return void
     */
    public function saveImage(object $post)
    {
        $sessionId = Session::getId();
        // post時に作成した、temp下のSession ID名ディレクトリのパスを取得する
        $tempPath = storage_path('app/public/temp/'.$sessionId);
        if(file_exists($tempPath)) {
            // prod下にSession ID名でディレクトリを作成
            mkdir(storage_path('app/public/prod/'.$sessionId), '0777');
            // temp下のSession ID名ディレクトリのハンドルがオープンである場合、以下の処理をする
            if ($handle = opendir($tempPath)) {
                // ディレクトリハンドルからエントリを順番に読み込む
                while (false !== ($file = readdir($handle))) {
                    // file名が"."、又は".."でなければ、ファイルごとに以下の処理をする
                    if ($file != "." && $file != "..") {
                        // temp下Session IDディレクトリ内のfileをprod下のSession IDディレクトリに移動する
                        rename("$tempPath/$file", "$tempPath/../../prod/$sessionId/$file");
                        // imagesテーブルにレコードを保存
                        $image = array('post_id' => $post->id, 'image' => $file, 'session_id' => $sessionId);
                        $this->image_model->createImage($image);
                    }
                }
            }
        }
    }

}

