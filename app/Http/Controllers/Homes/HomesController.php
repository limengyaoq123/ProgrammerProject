<?php

namespace App\Http\Controllers\Homes;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Model\cinema;
use App\Http\Model\cininfo;
use App\Http\Model\film;
use Hash;
use DB;

class HomesController extends Controller
{

    //主页
    public function index()
    {   
        //热映电影数据
        $res = film::orderBy('shownum','desc')->limit('3')->get();
        //加载首页
        return view('homes/index',['res' => $res]);
    }


    //电影列表
    public function filmlist()
    {

        //电影列表数据
        $res = film::paginate(2);

        //电影排行榜数据
        $res1 = film::orderBy('shownum','desc')->limit('3')->get();

       //加载电影列表
        return view('homes/filmlist',['res' => $res,'res1'=>$res1]);
    }


    //电影详情页
    public function filmdetail(Request $request)
    {
        //电影详情数据
        $res = film::find($request->only('id'));
        //加载电影详情数据
        return view('homes/filmdetail',['res' => $res]);
    }


    //电影院列表
    public function cinemalist()
    {
        $res = cinema::get();

        return view('homes/cinemalist');
    }


    //电影院详情
    public function cinemadetail()
    {

        return view('homes/cinemadetail');
    }




    //申请商户
    public function add()
    {
        return view('homes/shenqing');
    }

    public function store(Request $request)
    {
        $res = $request->except('_token','city','area','address');
        $res1 = $request->only('city','area','address');

        $res['password'] = Hash::make($res['password']);


        if($request -> hasFile('license'))
        {

           //文件名
            $name = rand(11111,99999).time();

            //获取后缀名
            $jpg = $request -> file('license')->getClientOriginalExtension();
          
            //移动图片
            $request ->file('license') -> move('./public/FilmPublic/Uploads',$name.'.'.$jpg); 
        }  

        $license = './public/FilmPublic/Uploads/'.$name.$name.'.'.$jpg;
        $res['license'] = $license;

        //事务处理
        DB::beginTransaction();

        $cinema = cinema::insert($res);
        $cininfo = cininfo::insert($res1);

        //判断
        if($cinema && $cininfo)
        {   
            DB::commit();
            return redirect('/homes/index'); 

        }else{
            
            DB::rollback();
        }

    }    


    //搜索的页面
    public function search(Request $request)
    {
        //获取要搜索的字段
        $seach = implode($request->all());

        $res = film::where('filmname','like','%'.$seach.'%')->get();

        //加载模糊搜索匹配的电影列表
        return view('homes/search',['res' => $res]);

    }
        
    





}
