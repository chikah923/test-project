<?php

namespace App\Http\Controllers\Auth;

use App\Model\User as UserModel;
use App\Model\VerifyUser as VerifyUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Mail;
use App\Mail\VerifyMail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserModel $user_model, VerifyUser $verify_user_model)
    {
        $this->middleware('guest');
        $this->user_model = $user_model;
        $this->verify_user_model = $verify_user_model;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                // usersテーブルにユーザを新規保存
                $user = $this->user_model->createUser($data);
                // verify_usersテーブルにユーザを新規保存
                $this->verify_user_model->createVerifyUser($user);
                // ユーザのemail宛に確認メールを送信する
                Mail::to($user->email)->send(new VerifyMail($user));
                return $user;
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

     /** 送信メールのリンク"Verify Email"が押下された際以下の処理を行う
     *
     * @access public
     * @param  String $token
     * @return response
     */
    public function verifyUser($token)
    {
        // verify_usersテーブル中のカラム"token"から、リクエストで受け取ったtokenと等しいtokenを持つレコードを取得
        $verifyUser = $this->verify_user_model->getVerifyUserWithToken($token);
        // $verifyUserがnullでない場合、以下の処理を行う
        if(isset($verifyUser)){
            // $verifyUserに該当するユーザがusersテーブル上でカラム"verified"に値"0"を持つ場合
            $user = $verifyUser->user;
            if(!$user->verified) {
                try {
                    DB::transaction(function () use ($user) {
                        // カラム"verified"の値を"1"に更新して保存し、以下のステータスメッセージを定義する
                        $id = $user->id;
                        $this->user_model->updateVerification($id);
                        $status = "Your e-mail is verified. You can now login.";
                    });
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            } else {
                // $verifyUserがnullでなく、カラム"verified"が既に"1"となっている場合、以下のステータスメッセージを定義する
                $status = "Your e-mail is already verified. You can now login.";
            }
        } else {
                // $verifyUserがnullの場合、以下の警告メッセージを定義する
                return redirect('/login')->with('warning', "Sorry your email cannot be identified.");
        }
        return redirect('/login')->with('status', $status);
    }

   /**
    * The user has been registered.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  mixed  $user
    * @return mixed
    */
    protected function registered($request, $user)
    {
        $this->guard()->logout();
        return redirect('/login')->with('status', 'We sent you an activation code. Check your email and click on the link to verify.');
    }

}

