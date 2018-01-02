<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\model\cinema;
use DB;
use Hash;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   

        $res = DB::table('cinema')    

             //将三张表拼接起来 
             //链接两个表的方法join
             //join('表名','主表id','与主表关联的附表的关联id')
            ->join('cininfo','cininfo.cid', '=', 'cinema.id')->join('cinlogin','cinlogin.cid', '=', 'cinema.id')  

            //选择需要用的字段查询
            ->select('cinema.id','cinema.cinema','cinema.phone','cinema.clogo','cinema.time','cinema.legal','cinema.status','cininfo.city','cininfo.area','cininfo.address','cinlogin.cinema','cinlogin.time','cinlogin.status')
            ->where('cinema.cinema','like','%'.$request->input('search').'%')
            ->where('cinema.status','>',1)
            ->orderBy('cinema.id','asc')
            ->paginate($request->input('num',10));

            
            return view('admin.cinema.index',['res'=>$res,'request'=>$request]);
            
            
        


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //
       return view('admin.cinema.add');


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        //var_dump($request);die;
        //用only方法拿出添加页面传递过来的你需要的数据存入$input1
        $input1 = $request->only('cinema','password','phone','legal','clogo','time','status');

        //哈希加密
        $input1['password'] = Hash::make($input1['password']);

        // $time = time();
      
        $input1['time'] = time();
        
        //图片上传的判断
        if($request -> hasFile('clogo'))
        {

           //文件名
            $name = rand(111,555).time();

            //获取后缀名
            $jpg = $request -> file('clogo')->getClientOriginalExtension();
          
            //移动图片
            $request ->file('clogo') -> move('./adminsUplode/',$name.'.'.$jpg); 
        }  

        $clogo = './adminsUplode/'.$name.'.'.$jpg;

        //把拼装好的clogo名字存入$input1['clogo']
        $input1['clogo'] = $clogo;

        // var_dump($input1);die;
        
        //定义一个变量$cinema存查询cinema表的id
        $cinema = DB::table('cinema')->insertGetId($input1);

        //判断$cinema为ture或false
        //为ture时证明id传过来了
        //false就没有值传递过来
        if($cinema){

            //开启事务
            DB::beginTransaction();

            $input2 = $request->only('city','area','address');
            $input3 = $request->only('cinema','password','status');
            
            //把传递过来的id赋值给$input2['cid']
            $input2['cid'] = $cinema;
            $input3['cid'] = $cinema;


            $input3['password'] = $input1['password'];

            $ctime = time();
            $input3['time'] = $ctime;

            $cininfo = DB::table('cininfo')->insert($input2);
            $clogo = DB::table('cinlogin')->insert($input3);

            if($cinema && $cininfo && $clogo)
            {   
                DB::commit();
                return redirect('/admin/cinema'); 

            }else{
                //回滚
                DB::rollback();
            }
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $res = DB::table('cinema')    

             //将两张表拼接起来 
             //链接两个表的方法join
             //join('表名','主表id','与主表关联的附表的关联id')
            ->join('cininfo','cinema.id', '=', 'cininfo.cid')   

            //选择需要用的字段查询
            ->select('cinema.id','cinema.cinema','cinema.phone','cinema.clogo','cinema.time','cinema.legal','cininfo.cid','cininfo.city','cininfo.area','cininfo.address')->where('cinema.id',$id)->first();

            // echo "<pre>";
            // var_dump($res);die;
        return view('admin.cinema.edit',['res'=>$res]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

          //判断是否有文件上传
        if($request->hasFile('clogo')){
            
            //先查询
            $find = cinema::where('id',$id)->first();
            //判断图片是否存在  存在就删除
            //遍历图片<img src="{{asset($find->logo)}}">
                              
            if(file_exists($find->clogo))
             {
                unlink($find->clogo);
             }

            //获取文件名
            $name=rand(1111,9999).time();

            //获取文件名后缀
            $hz=$request->file('clogo')->getClientOriginalExtension();

            //移动文件
            $request->file('clogo')->move('./adminsUplode/',$name.'.'.$hz);
        }else{

            return back()->with('msg','请上传网站logo!');
            die;
        }

        $input1 = $request->except('_token','_method','city','area','address');
        $input2 = $request->except('_token','_method','cinema','phone','legal','status','clogo');
        $input3 = $request->except('_token','_method','phone','legal','clogo','city','area','address');
        
        // echo "<pre>";
        // var_dump($input3);die;   
        // $ress = user::where('id',$id)->update($input);
        
        $input1['clogo'] = './adminsUplode/'.$name.'.'.$hz;

        $res = DB::table('cinema')->where('id',$id)->update($input1);
        $res = DB::table('cininfo')->where('cid',$id)->update($input2);
        $res = DB::table('cinlogin')->where('cid',$id)->update($input3);

        if($res){
             return redirect('/admin/cinema');
           
        }else{
            return back();
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        DB::beginTransaction();

        $res1 = DB::table('cinema')->where('id',$id)->delete();
        $res2 = DB::table('cininfo')->where('cid',$id)->delete();
        $res3 = DB::table('cinlogin')->where('cid',$id)->delete();

        
        if($res=1 || $res=0){
            DB::commit();
            return redirect('/admin/cinema')->with('删除成功');
        }else{
            return back();
        }


    }
}
