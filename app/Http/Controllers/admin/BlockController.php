<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\model\friendlink;
use DB;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $res = DB::table('friendlink')
        ->where('linkname','like','%'.$request->input('search').'%')
        ->orderBy('id','asc')
        ->paginate($request->input('num',10));

        // echo"<pre>";
        // var_dump($res);die;
        return view('admin.block.index',['res'=>$res,'request'=>$request]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

         return view('admin.block.add');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->except('_token','_method');

        // var_dump($input);die;
        $res = friendlink::insert($input);

        // echo "<pre>";
        // var_dump($res);die;

        if($res){
            return redirect('/admin/block');
        }else{
            return back();
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
        $res = friendlink::find($id);

        return view('admin.block.edit',['res'=>$res]);
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

        $input = $request->except('_token','_method');
        
        $res = DB::table('friendlink')->where('id',$id)->update($input);

        if($res){
            return redirect('/admin/block');
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
        $res=DB::table('friendlink')->where('id',$id)->delete();

        // var_dump($res);die;
         if($res){
             return redirect('/admin/user')->with('删除成功');
         }else{
             return back();
         }
    }
}