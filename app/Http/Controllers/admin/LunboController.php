<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Model\lunbo;


use zgldh\QiniuStorage\QiniuStorage;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;

class LunboController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $res=lunbo::all();
        // dd($res);
        return view('admin.lunbo.index',compact('res'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

         return view('admin.lunbo.add');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

         //判断是否有文件上传
       if($request->hasFile('picname')){
           
              //获取文件
                $file=$request->file('picname');
                //初始化七牛
                $disk=QiniuStorage::disk('qiniu');

                //重命名文件名
                $name=md5(rand(1111,9999).time()).'.'.$file->getClientOriginalExtension();

                //上传到文件到七牛
                $bool=$disk->put('Uplodes/image_'.$name,file_get_contents($file->getRealPath()));

            }else{
              
                //如果没有文件上传  判断是否输入了电影名称  
                if($request->only('fname')){

                    return back()->with('lbt','请上传轮播图或电影名称!');
                    die;
                }
            
        }

        //如果有文件上传了  检测是否存在电影名称的值
       if($request->has('fname')){
            $res = $request->except('_token');

            //修改上传轮播图的名字
            //修改上传logo的名字
            $res['picname'] = 'image_'.$name;

            //获取上传时间
            $res['time']=time();

            //插入到数据库
            $sql=lunbo::insert($res);

            if($sql){

                return redirect('/admin/lunbo')->with('msg','添加成功');
            }else{

                return back()->withInput();
            }
          
        }else{

             return back()->with('lbt','请输入电影名称!');
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

        $sql=lunbo::find($id);
        // dd($res);
        return view('admin.lunbo.edit',compact('sql'));

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
            if($request->hasFile('picname')){
            
                /*删除七牛文件的方法*/
                /*在上传文件前先删除此文件  再上传新的文件并修改数据库的信息*/

                //从数据库查询
                $find=lunbo::find($id);
                // var_dump($find);die;

                $accessKey = '6KNr_k8cHOhY8vRfsoVVQDOsepKnzYgh7gxMqg0w';
                $secretKey = 'USietl53216m7raLRSEVuXwYEwxwEs3ZR1hQ5hKZ';

                //初始化Auth状态：
                $auth = new Auth($accessKey, $secretKey);

                //初始化BucketManager
                $bucketMgr = new BucketManager($auth);

                //你要测试的空间， 并且这个key在你空间中存在
                $bucket = 'laravel-upload';
                $key = "Uplodes/".$find->picname;
                // var_dump($find->logo);

                //删除$bucket 中的文件 $key   删除七牛里的文件
                $err = $bucketMgr->delete($bucket,$key);

                /*执行文件上传*/

                //获取文件
                $file=$request->file('picname');
                //初始化七牛
                $disk=QiniuStorage::disk('qiniu');

                //重命名文件名
                $name=md5(rand(1111,9999).time()).'.'.$file->getClientOriginalExtension();

                //上传到文件到七牛
                $bool=$disk->put('Uplodes/image_'.$name,file_get_contents($file->getRealPath()));
                
                //判断上传到七牛是否成功
                if($bool){
                    //http:/Uplodes/image_981e101cc9a0efecb77f7bb3b7129525.jpg
                   // $path=$disk->downloadUrl('Uplodes/image_'.$name);
                    $res = $request->except('_token','_method');

                    //修改上传logo的名字
                    $res['picname'] = 'image_'.$name;

                    //将修改后的文件名插入到数据库
                    $sql=lunbo::where('id',$id)->update($res);

                        //判断是否插入数据库成功
                        if($sql){

                            return redirect('/admin/lunbo')->with('msg','修改成功');
                        
                        }else{

                            return back();
                        }

                }else{
                    return "上传失败";                    
                }
                    
                    
            }else{

                return back()->with('msg','请上传文件');
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

     
            /*删除七牛文件的方法*/
            /*先删远程文件 再删数据库文件*/

            //从数据库查询
            $find = lunbo::where('id',$id)->first();
           
            $accessKey = '6KNr_k8cHOhY8vRfsoVVQDOsepKnzYgh7gxMqg0w';
            $secretKey = 'USietl53216m7raLRSEVuXwYEwxwEs3ZR1hQ5hKZ';

            //初始化Auth状态：
            $auth = new Auth($accessKey, $secretKey);

            //初始化BucketManager
            $bucketMgr = new BucketManager($auth);

            //你要测试的空间， 并且这个key在你空间中存在
            $bucket = 'laravel-upload';
            $key = "Uplodes/".$find->picname;
            // var_dump($find->logo);

            //删除$bucket 中的文件 $key   删除七牛里的文件
            $err = $bucketMgr->delete($bucket,$key);
             

            //执行删除
            $sql=lunbo::where('id',$id)->delete();

            if($sql){

                return redirect('/admin/lunbo')->with('msg','删除成功');    
            }else{
                
                return back();
            }

    }

}