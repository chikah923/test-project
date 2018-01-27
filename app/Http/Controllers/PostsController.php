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
use Session;
use Auth;
use DB;

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
    * @param  String[] $request
    * @return response
    */
    public function index(Request $request)
    {
        // Loginユーザではない場合、ユーザ名以外のデータをviewに渡す
        if (is_null($request->user())){
            return view ('posts.index')->with([
                'posts' => $this->post_model->getAllAuthedPost(),
                'pager_link' => $this->post_model->getAllAuthedPost()->links(),
                'tags' => $this->tag_model->getAllTag()
            ]);
        }
        // Loginユーザの場合、ユーザ名情報も含めたデータをviewに渡す
        return view ('posts.index')->with([
            'posts' => $this->post_model->getAllAuthedPost(),
            'user' =>$request->user()->name,
            'pager_link' => $this->post_model->getAllAuthedPost()->links(),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /** 入力内容をFlashに保存し、確認画面にリダイレクトする
    *
    * @access public
    * @param  String[] $request
    * @return response
    */
    public function post(PostRequest $request)
    {
        // File以外の入力情報をセッションに保存する
        $request->session()->regenerate();
        Session::put('entry', $request->except('featured_image'));

        // Requestにタグ情報の入力がある場合、以下の処理をする
        if ($request->has('tags')) {
            $tags = $request->tags;
            $tagRecords = array();
            foreach ($tags as $tagId) {
                array_push($tagRecords, $this->tag_model->getTagName($tagId)->name);
            }
        }

        // RequestにFile uploadが含まれている場合、以下の処理をする
        if ($request->hasFile('featured_image')) {
            $images = $request->file('featured_image');
            // 各Fileに対し以下の処理をする
            $imageArray = array();
            foreach ($images as $file) {
                // File名を取得
                $fileName = $file->getClientOriginalName();
                // Session IDの取得
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
                // 2時間後にfileとSeddion Id名のディレクトリを削除する
                $expire = strtotime("2 hours ago");
                if(filemtime($location)<$expire) {
                    unlink($location);
                    rmdir($path);
                }
            }
        }
                    if ($request->has('tags') and $request->hasFile('featured_image')) {
                        return view('posts/confirm')->with([
                        'input' => $request->all(),
                        'images' => $imageArray,
                        'tags' => $tagRecords,
                        ]);
                    } elseif ($request->has('tags')) {
                        return view('posts/confirm')->with([
                        'input' => $request->all(),
                        'tags' => $tagRecords,
                        ]);
                    } elseif ($request->hasFile('featured_image')) {
                        return view('posts/confirm')->with([
                        'input' => $request->all(),
                        'images' => $imageArray,
                        ]);
                    }
                        return view('posts/confirm')->with([
                        'input' => $request->all(),
                        ]);
    }

    /** postデータの新規保存
    *
    * @access public
    * @param  String[] $request
    * @return response
    */
    public function store(Request $request)
    {
        // 保存しておいたセッション情報を取得する
        $input = Session::get('entry');
        /* 確認画面で戻るボタンが押された場合 */
        if ($request->get('action') === 'Back') {
            //入力画面へ戻る
            return redirect('/')
                ->withInput($input);
        }
        try {
            DB::transaction(function () use ($input) {
                /* postデータを保存する */
                $post = $this->post_model->createPost($input);
                /* Requestにtagの入力があった場合、中間テーブルtag_postにレコードを挿入する */
                 if(isset($input['tags'])) {
                    $tag = $input['tags'];
                    $this->post_model->createTagPost($post, $tag);
                 }

                /* RequestにFile uploadが含まれていた場合、以下の処理をする */
                $sessionId = Session::getId();
                // post時に作成した、temp下のSession ID名ディレクトリのパスを取得する
                $tempPath = storage_path('app/public/temp/'.$sessionId);
                // temo下にSession Id名のディレクトリが存在すれば、以下の処理をする
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
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return view('posts/completed');
    }


    /** 該当するpostデータの削除
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function destroy(int $id, Request $request)
    {
        if (is_null($request->user())){
            return view ('posts.error_exception');
        }
        /* 該当するpostデータにFileが存在する場合、public/imagesディレクトリから画像を削除する */
        $images = $this->image_model->getImageOfPostId($id);
        try {
            DB::transaction(function () use ($images, $id) {
                foreach ($images as $image) {
                    File::delete(storage_path('app/public/prod/'.$image->session_id. '/'.$image->image));
                }
                $this->post_model->deletePost($id);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return redirect('/');
    }

    /** 該当するpostデータの編集画面に移動
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function edit(int $id)
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
        try {
            DB::transaction(function () use ($request) {
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
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return redirect('/');
    }

    /** 該当するpostデータの表示
    *
    * @access public
    * @param  int $id
    * @return response
    */
    public function show(int $id)
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
        // 検索フォームに入力された文字列を取得する
        $keyword = $request->input('keyword');
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

    /** Loginユーザの場合管理者用のページを表示し、それ以外の場合はメッセージを表示する
    *
    * @access public
    * @param String[] $request
    * @return response
    */
    public function adminIndex(Request $request)
    {
        // Loginユーザではない場合、エラーの画面を返す
        if (is_null($request->user())){
            return view ('posts.error_exception');
        }
        // Loginユーザの場合、postsテーブルにあるレコードのうち、カラムchk_flgがFalseのものを表示する
        return view ('posts.index_auth')->with([
            'user' =>$request->user()->name,
            'posts' => $this->post_model->getAllPostToBeAuth(),
            'pager_link' => $this->post_model->getAllPostToBeAuth()->links(),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /** 未承認postの承認(該当するpostsテーブルレコードのカラムchk_flgの値をFalseからTrueにupdateする)
    *
    * @access public
    * @param String[] $request
    * @return response
    */
    public function authPost(Request $request)
    {
        // requestから、Checkboxを選択されたpost idの配列を取得する
        $idArray = $request['chk_flg'];
        try {
            DB::transaction(function () use ($idArray) {
                // カラムchk_flgの値をFalseからTrueにupdateする
                foreach ($idArray as $id) {
                    $this->post_model->UpdateColumnChkFlg($id);
                }
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        // postsテーブルにあるレコードのうち、カラムchk_flgがFalseのものを表示する
        return view ('posts.index_auth')->with([
            'user' =>$request->user()->name,
            'posts' => $this->post_model->getAllPostToBeAuth(),
            'pager_link' => $this->post_model->getAllPostToBeAuth()->links(),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

}

