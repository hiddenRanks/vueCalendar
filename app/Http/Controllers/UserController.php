<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $req)
    {
        $loginData = $req->only('email', 'password');

        if(auth()->attempt($loginData)){
            //로그인 성공
            return response()->json(['result'=>true, 'msg'=>'로그인 되었습니다.', 'user'=>auth()->user()]);
        }else {
            //실패
            return response()->json(['result'=>false, 'msg'=>'로그인 실패 아이디와 비밀번호를 확인하세요']);
        }
    }

    public function loginStatus(Request $req)
    {
        if(auth()->check()){
            return response()->json(['result'=>true, 'user'=>auth()->user()]); //로그인되어있을때 유저 보내주기
        }else{
            return response()->json(['result'=>false, 'user'=>null]); //미로그인시 null
        }
    }

    public function logout(Request $req)
    {
        auth()->logout();
        return response()->json(['success'=>true, 'msg'=>'로그아웃되었습니다.']);
    }
}
