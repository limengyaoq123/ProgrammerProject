<?php

namespace App\Http\Controllers\Homes;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Model\user;

use Hash;


class HomesLoginController extends Controller
{
    //
    public function index()
    {
    	return view('homes/login');
    }

    public function dologin(Request $request)
    {
        
        // $res = $request->except('_token');
        $res = $request->only('phone');
       

    	$uname = user::where('phone',$res)->first();
        // dd($uname);

        // echo "<pre>";
        $a = $uname['password'];   
        $b = $request->input('password');
       
        // $pass =Hash::check($request->input('password'),$uname['password']);
        // $pass = Hash::check($a,$b);
      


        if(!$uname){

              return redirect('/homes/login')->with('status','用户名或密码错误,请重新登录');
        }

        if(!$a==$b){

            return redirect('/homes/login')->with('status','用户名或密码错误,请重新登录');
        }

        $request->session()->put('uid',$uname->id);
        $request->session()->put('uphone',$uname->phone);

        return redirect('/homes/index');
        

    }

}
