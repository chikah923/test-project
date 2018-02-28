<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    protected $guarded = [];

    /**
     * usersテーブルとの紐付きを設定
     *
     * @access public
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }

    /**
     * userの新規保存
     *
     * @access public
     * @param obj $user
     * @return void
     */
    public function createVerifyUser(object $user)
    {
        return $this->create([
            'user_id' => $user->id,
            'token' => str_random(40),
        ]);
    }

    /**
     * userからのリクエストで受け取ったtokenと等しいtokenを持つレコードを取得
     *
     * @access public
     * @param string $token
     * @return void
     */
    public function getVerifyUserWithToken(string $token)
    {
        return $this->where('token', $token)->first();
    }

}

