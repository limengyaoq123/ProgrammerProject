<?php

namespace App\Http\Controllers\Homes;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Model\ticket;
use App\Http\Model\user;
use Session;

class HomesCenterController extends Controller
{
    //
     public function index()
    {
    	
    	if(!session('uid')){
    		
    		return view('/homes/login');
    		die;
    	}
    	return view('/homes/center');
    }

}
