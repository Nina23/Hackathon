<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parents;
use Validator;
use Hash;
use App\ParentsChild;
use App\Child;



class ParentsController extends Controller
{
    public function storeParents(Request $request){

//        return print_r($request['MAIL']);
        
         $rules = array(
            'MAIL'=>'email|required',
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
               ];
        
        
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
    
    
    public function loginParents(Request $request){
         $rules = array(
            'MAIL'=>'email|required',
            'PASSWORD'=>'required'
            
        );
         
        $validator = Validator::make($request->all(),$rules);

       
        if($validator->fails()){
            return response()->json(['error'=>'validacija']);
        }
        
       $parent=  Parents::where('email',$request['MAIL'])->first();
       
       if($parent==null)
       {
            $response=['SUCCESS'=>false];
            return response()->json($response);
       }
       
       
       if (Hash::check($request['PASSWORD'], $parent['password'])) {
           
           $parents_child= ParentsChild::where('parents',$parent->id)->get();
           $list_children=[];
           $counter=0;
           foreach($parents_child as $child_id){
               
               $child=Child::where('id',$child_id->child)->first();
              
               if($child!=null){
                   $list_children[$counter]=['CHILD_ID'=>$child->id,'UNIQUE_ID'=>$child->unique_id];
                   $counter++;
                  
               }
           }
            $response=['SUCCESS'=>true,'CHILDREN'=>  array_values($list_children)];
            return response()->json($response);
       }
       else{
            $response=['SUCCESS'=>false];
            return response()->json($response);
       }
        
    }
}
