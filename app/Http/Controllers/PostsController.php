<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Requests\PostRequest;
use App\Model\Post as PostModel;
use App\Model\Image as ImageModel;
use App\Model\Tag as TagModel;
use Image;
use File;

class PostsController extends Controller
{
    private $post_model;
    private $image_model;
    private $tag_model;
    /** Postモデルをインスタンス化する
    *
    * @access public
    * @param obj $post_model
    * @return void
    */
    public function __construct(PostModel $post_model, ImageModel $image_model, TagModel $tag_model)
    {
        $this->post_model = $post_model;
        $this->image_model = $image_model;
        $this->tag_model = $tag_model;
    }

    /** 既存のpostsデータ件取得する
    *
    * @access public
    * @return response
    */
    public function index()
    {
        return view ('posts.index')->with([
            'posts' => $this->post_model->getAllPost(),
            'pager_link' => $this->post_model->getAllPost()->links(),
            'tags' => $this->tag_model->getAllTag()
        ]);
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
        $post = $this->post_model->createPost($input);


        /* tagの入力があった場合、中間テーブルtag_postにレコードを挿入する */
        if($request->has('tags')) {
        $tag = $request->tags;
            $this->post_model->createTagPost($post, $tag);
        }

        /* RequestにFile uploadが含まれている場合、以下の処理をする */
        if ($request->hasFile('featured_image')) {
            $images = $request->file('featured_image');
            // 各Fileに対し以下の処理をする
            foreach ($images as $file) {
                // File名を取得
                $fileName = $file->getClientOriginalName();
                // Fileの保存先を$loacationに格納
                $location = public_path('images/'. $fileName);
                // Fileをリサイズして$locationに保存
                Image::make($file)->resize(200, 100)->save($location);
                $image = array('post_id' => $post->id, 'image' => $fileName);
                // imagesテーブルにFile名を保存する
                $this->image_model->createImage($image);
             }
        }
         return redirect('/');
    }


    /** 該当するpostデータの削除
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function destroy($id)
    {
        /* 該当するpostデータにFileが存在する場合、public/imagesディレクトリから画像を削除する */
        $images = $this->image_model->getImageOfPostId($id);
        //$images = $post->images;
        foreach ($images as $image)
        {
            File::delete(public_path('images/'.$image->image));
        }
        $this->post_model->deletePost($id);
        return redirect('/');
    }

    /** 該当するpostデータの編集画面に移動
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function edit($id)
    {
        return view('posts.edit')->with([
            'post' => $this->post_model->getPostFromId($id),
            'images' => $this->image_model->getImageOfPostId($id),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /** 該当するpostデータの更新
    *
    * @access public
    * @param  String[] $request
    * @return response
    */
    public function update(PostRequest $request)
    {
        /* RequestにFile uploadが含まれている場合、以下の処理をする */
        if ($request->hasFile('featured_image')) {
            $images = $request->file('featured_image');
            //各Fileに対し以下の処理をする
            foreach ($images as $file) {
                // File名を取得
                $fileName = $file->getClientOriginalName();
                // Fileの保存先を$loacationに格納
                $location = public_path('images/'. $fileName);
                // Fileをリサイズして$locationに保存
                Image::make($file)->resize(400, 200)->save($location);
                $image = array('post_id' => $request->id, 'image' => $fileName);
                // imagesテーブルにFile名を保存する
                $this->image_model->createImage($image);
            }
        }

        /* $requestからパラメータの配列のみ取得し$inputに格納 */
        $input = $request->all();
        $this->post_model->updatePost($input);
        return redirect('/');
    }

    /** 該当するpostデータの表示
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function show($id)
    {
        return view('posts.show')->with([
            'post' => $this->post_model->showPost($id),
            'images' => $this->image_model->getImageOfPostId($id),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /** 文字列検索機能
    *
    * @access public
    * @param  String[] $request
    * @return response
    */
    public function search(Request $request)
    {
        //検索フォームに入力された文字列を取得する
        $keyword = $request->input('keyword');

        //query()て何？modelに移動？
        $query = PostModel::query();
        if (!empty($keyword)) {
            $query->where('name', 'like', '%'.$keyword.'%')
                  ->orWhere('body', 'like', '%'.$keyword.'%');
            $posts = $query->orderBy('created_at','desc')->paginate(10);
        }else {
            $posts = $this->post_model->getAllPost();
        }
        return view('posts.index')->with([
            'posts' => $posts,
            'pager_link' => $posts->links(),
            'keyword' => $keyword,
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /** "updated_at"の降順に記事をソートする
    *
    * @access public
    * @return response
    */
    public function sortByLastUpdated()
    {
        return view('posts.index')->with([
            'posts' => $this->post_model->getAllPostByLastUpdated(),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

}

