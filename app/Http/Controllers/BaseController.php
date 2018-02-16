<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

abstract class BaseController extends Controller
{
    public function __construct()
    {
    }

    /** actionに対して指定したView/Viewに渡すデータを渡す
    *
    * @access protected
    * @return response view
    * @param  String[] $view_vars
    */
    protected function render(array $view_vars = [])
    {
        return view($this->getViewRoute(), $view_vars);
    }

    /** actionに対して指定したView/Viewに渡すデータを渡す
    *
    * @access protected
    * @return String
    */
    protected function getViewRoute()
    {
        $class_name  = class_basename(Route::currentRouteAction());
        $view_route = str_replace('Controller@', '/', $class_name);

        return snake_case($view_route);
    }
}

