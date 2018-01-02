<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class RequestController extends Controller
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
            ->where('cinema.status','<',2)
            ->orderBy('cinema.id','asc')
            ->paginate($request->input('num',10));

            // echo "<pre>";
            // var_dump($res);die;
            return view('admin.request.index',['res'=>$res,'request'=>$request]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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
            ->join('cinlogin','cinlogin.cid', '=', 'cinema.id')   
            ->select('cinema.id','cinema.status','cinlogin.status')
            //选择需要用的字段查询
            ->where('cinema.id',$id)
            ->orwhere('cinema.status','=',1)->first();
            $aa = $res->status == 1 ? 2 : 1;

            // echo "<pre>";
            // var_dump($res);die; 
            $ress = DB::table('cinema')->where('id',$id)->update(['status'=>$aa]);
            $ress2 = DB::table('cinlogin')->where('cid',$id)->update(['status'=>$aa]);

        //     echo "<pre>";
        //     var_dump($res);die;
        return redirect('/admin/request');
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

        $res = DB::table('cinema')->where('id',$id)->delete();
        $res = DB::table('cinlogin')->where('cid',$id)->delete();

        if($res=1 || $res=0){
            DB::commit();
            return redirect('/admin/request')->with('删除成功');
        }else{
            return back();
        }
    }
}
