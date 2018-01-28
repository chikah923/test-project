<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    protected $guarded = [];

    /** usersテーブルとの紐付きを設定
     *
     * @access public
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

}

