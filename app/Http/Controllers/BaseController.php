<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

abstract class BaseController extends Controller
{
    public function __construct()
    {
    }

    /**
     * viewのパス、及び渡すデータの配列を取得してviewを返す
     *
     * @access protected
     * @param string[] $view_vars
     * @return response View
     */
    protected function render(array $view_vars = [])
    {
        return view($this->getViewRoute(), $view_vars);
    }

    /**
     * コントローラ名、及びメソッド名を取得しviewへのパスをstringで返す
     *
     * @access protected
     * @return string //Viewへのバス
     */
    protected function getViewRoute()
    {
        $class_name  = class_basename(Route::currentRouteAction());
        $view_route = str_replace('Controller@', '/', $class_name);
        return snake_case($view_route);
    }
}

