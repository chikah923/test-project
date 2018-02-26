<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Tag as TagModel;

class TagsController extends Controller
{
    private $comment_model;

    /**
     * TagModelのインスタンスを作成する
     *
     * @access public
     * @param obj $tag_model
     * @return void
     */
    public function __construct(TagModel $tag_model)
    {
        $this->tag_model = $tag_model;
    }

}

