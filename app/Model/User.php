<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /** verify_usersテーブルとの紐付きを設定
     *
     * @access public
     * @return void
     */
    public function verifyUser()
    {
        return $this->hasOne('App\Model\VerifyUser');
    }

    /** userの新規保存
     *
     * @access public
     * @param  String[] $data
     * @return void
     */
    public function createUser(array $data)
    {
       return $this->create([
           'name' => $data['name'],
           'email' => $data['email'],
           'password' => bcrypt($data['password']),
       ]);
    }

    /** 該当userのカラム"veried"を"0"から"1"に更新して認証済みuserとする
     *
     * @access public
     * @param  String[] $user
     * @return void
     */
    public function updateVerification($id)
    {
        return $this->where('id', $id)
                    ->update(['verified' => 1]);
    }

}

