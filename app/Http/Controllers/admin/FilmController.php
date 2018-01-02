<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Model\filmtype;
class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $res=filmtype::all();
        return view('admin.film.index',compact('res'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.film.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $res=$request->except('_token');


        $sql=filmtype::insert($res);

        if($sql){

            return redirect('/admin/film')->with('msg','添加成功');
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
        $res=filmtype::find($id);

        return view('admin.film.edit',compact('res'));
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
       $res=$request->except('_token','_method');

        $sql=filmtype::where('id',$id)->update($res);

        if($sql){

            return redirect('/admin/film')->with('msg','修改成功');
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

        $sql=filmtype::where('id',$id)->delete();

        if($sql){
            return redirect('/admin/film')->with('msg','删除成功');
        }else{
            return back();
        }
    }
}
