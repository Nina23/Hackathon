<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parents;
use Validator;



class ParentsController extends Controller
{
    public function storeParents(Request $request){
       
         $rules = array(
            'MAIL'=>'email|required|unique:parents',
            'PASSWORD'=>'required',
            'NAME'=>'required',
            'SURNAME'=>'required',
            'PHONE'=>'required'
        );
         

        $validator = Validator::make($request->all(),$rules);
       
        if($validator->fails()){
            return response()->json(['error'=>'validacija']);
        }
         $unique =  uniqid().'_'.uniqid();
      
        $parent_data=['unique_id'=>$unique,
                'email'=>$request['MAIL'],
                'password'=>  bcrypt($request['PASSWORD']),
                'first_name'=> $request['NAME'],
                'last_name'=>$request['SURNAME'],
                'number'=>$request['PHONE'],
                'status'=>0,
                'address'=>'',
                'image'=>''];
        
         return print_r('$parent_data');

        
        
        try{
        $parent= Parents::create($parent_data);
        $response=['SUCCESS'=>true,'ID'=>$parent->id,'UNIQUE_ID'=>$parent->unique_id];
        return response()->json($response);
         }
         catch (\Exception $e) {
             $response=['SUCCESS'=>false];
            return response()->json($response);
         }
        
       
       
        
        
    }
}
