<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\ImagesController as ImagesController;
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

class PostsController extends BaseController
{
    private $post_model;
    private $image_model;
    private $tag_model;

    /**
     * PostModel、ImageModel、及びTagModelのインスタンスを作成する
     *
     * @access public
     * @param obj $post_model
     * @param obj $image_model
     * @param obj $tag_model
     * @return void
     */
    public function __construct(
        PostModel $post_model,
        ImageModel $image_model,
        TagModel $tag_model
    ) {
        parent::__construct();
        $this->middleware('check.name');
        $this->post_model = $post_model;
        $this->image_model = $image_model;
        $this->tag_model = $tag_model;
    }

    /**
     * 管理者承認済みのpostsデータを全件取得する
     *
     * @access public
     * @param obj $request
     * @return response View //index画面を返す
     */
    public function index(Request $request)
    {
        return $this->render([
            'posts' => $posts = $this->post_model->getAllAuthedPost(),
            'pager_link' => $posts->links(),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /**
     * 入力内容をSessionに保存し、確認画面にリダイレクトする
     *
     * @access public
     * @param obj $request
     * @return response View //確認画面を返す
     */
    public function post(PostRequest $request)
    {
        // File以外の入力情報をセッションに保存する
        $request->session()->regenerate();
        Session::put('entry', $request->except('featured_image'));
        // Requestにタグ情報が含まれている場合、それぞれのタグ名を取得する
        $tagArray = $this->confirmTag($request);

        // RequestにFile uploadが含まれている場合、画像をpublic/tempディレクトリに保存する
        if ($request->hasFile('featured_image')) {
             //$imageArray = $this->confirmImage($request);
            $imageArray = app('App\Http\Controllers\ImagesController')->confirmImage($request);
        }
                    if ($request->hasFile('featured_image')) {
                        return $this->render([
                        'input' => $request->all(),
                        'images' => $imageArray,
                        'tags' => $tagArray,
                        ]);
                    }
                        return $this->render([
                        'input' => $request->all(),
                        'tags' => $tagArray,
                        ]);
    }

    /**
     * Requestにタグ情報が含まれている場合、それぞれのタグ名を取得する
     *
     * @access private
     * @param obj $request
     * @return \ArrayObject<int>
     */
    private function confirmTag(PostRequest $request)
    {
        $tagArray = [];
        foreach ($request->tags as $tagId) {
            array_push($tagArray, $this->tag_model->getTagName($tagId)->name);
        }
        return $tagArray;
    }

    /**
     * postデータの新規保存
     *
     * @access public
     * @param obj $request
     * @return response View //投稿完了画面のviewを返す
     */
    public function savePost(Request $request)
    {
        $input = Session::get('entry');
        try {
            DB::transaction(function () use ($input) {
                $post = $this->post_model->createPost($input);
                $this->insertTagPost($input, $post);
                app('App\Http\Controllers\ImagesController')->saveImage($post);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return view('posts/completed');
    }

    /**
     * 投稿確認画面で戻るボタンが押された場合、入力内容を保持して入力画面に戻る
     *
     * @access public
     * @param obj $request
     * @return response
     */
    public function returnToIndex(Request $request)
    {
        $input = Session::get('entry');
        if ($request->get('action') === 'Return') {
            return redirect('/')
                ->withInput($input);
        }
    }

    /**
     * 投稿確認画面でconfirmされたRequest内容にtagの入力があった場合、
     * 中間テーブルtag_postにレコードを挿入する
     *
     * @access private
     * @param string[] $input
     * @param obj $post
     * @return void
     */
    private function insertTagPost(array $input, object $post)
    {
        if (isset($input['tags'])) {
            $tag = $input['tags'];
            $this->post_model->createTagPost($post, $tag);
        }
    }

    /**
     * 該当するpostデータの削除
     *
     * @access public
     * @param int $id
     * @param obj $request
     * @return response //index画面にリダイレクトする
     */
    public function destroyPost(int $id, Request $request)
    {
        if (is_null($request->user())){
            return view ('posts.error_exception');
        }
        try {
            DB::transaction(function () use ($id) {
                $images = $this->image_model->getImageOfPostId($id);
                app('App\Http\Controllers\ImagesController')->deleteImageFromPublic($images);
                $this->post_model->deletePost($id);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return redirect()->back();
    }

    /**
     * 該当するpostデータの編集画面に移動
     *
     * @access public
     * @param int $id
     * @return response View //edit画面を返す
     */
    public function edit(int $id)
    {
        return $this->render([
            'post' => $this->post_model->getPostFromId($id),
            'images' => $this->image_model->getImageOfPostId($id),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /**
     * 該当するpostデータの表示
     *
     * @access public
     * @param int $id
     * @return response View //show画面を返す
     */

    public function show(int $id)
    {
        return $this->render([
            'post' => $this->post_model->showPost($id),
            'images' => $this->image_model->getImageOfPostId($id),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /**
     * 文字列検索機能
     *
     * @access public
     * @param obj $request
     * @return response View //index画面を返す
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
        return $this->render([
            'posts' => $posts,
            'pager_link' => $posts->links(),
            'keyword' => $keyword,
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /**
     * "updated_at"の降順に記事をソートする
     *
     * @access public
     * @return response View //ソート済みindex画面を返す
     */
    public function sortByLastUpdated()
    {
        return view('posts.index')->with([
            'posts' => $this->post_model->getAllPostByLastUpdated(),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /**
     * Loginユーザの場合管理者用のページを表示し、それ以外の場合はメッセージを表示する
     *
     * @access public
     * @param obj $request
     * @return response View //エラー画面、または管理者用index画面を返す
     */
    public function adminIndex(Request $request)
    {
        // Loginユーザではない場合、エラーの画面を返す
        if (is_null($request->user())){
            return view ('posts.error_exception');
        }
        // Loginユーザの場合、postsテーブルにあるレコードのうち、カラムchk_flgがFalseのものを表示する
        return $this->render([
            'posts' => $posts = $this->post_model->getAllPostNeedAuth(),
            'pager_link' => $posts->links(),
            'tags' => $this->tag_model->getAllTag()
        ]);
    }

    /**
     * 未承認postの承認
     * (該当するpostsテーブルレコードのカラムchk_flgの値をFalseからTrueにupdateする)
     *
     * @access public
     * @param obj $request
     * @return response //管理者用index画面にリダイレクトする
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
        return redirect()->back();
    }

}

