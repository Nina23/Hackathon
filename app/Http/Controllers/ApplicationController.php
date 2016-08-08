<?php

namespace App\Http\Controllers;

use App\Applications;
use App\Child;
use App\UseApp;
use App\ScheduleNet;
use Illuminate\Http\Request;

use App\Http\Requests;

class ApplicationController extends Controller
{

    /*
     * Api for return black and white list and use application from white list
     * */


   public function blackWhiteList(Request $request){
       try {
           $child = Child::findOrFail($request['CHILD_ID']);
       }
       catch (\Exception $e){
           return response()->json(['error'=>'CHILD']);
       }

       $appliactions=Applications::where('child',$request['CHILD_ID'])->get();

       $black_list=[];
       $white_list=[];
       $counter_white=0;
       $counter_black=0;

       foreach($appliactions as $app){
           if($app->status==1) {
               $schedule_app=ScheduleApp::where('application',$app->id)->first();
               $white_list[$counter_white] = ['PACKAGE_NAME' => $app->name_of_package,'DAY'=>$schedule_app->day,'INTERVAL'=>$schedule_app->interval,'TIME'=>$schedule_app->time];
           }
           if($app->status==2)
               $black_list[$counter_black]=['PACKAGE_NAME'=>$app->name_of_package];
           $counter_white++;
           $counter_black++;
       }


       $response=["CHILD_ID"=>$request['CHILD_ID'],"APP_BLACKLIST"=>array_values($black_list),"APP_WHITELIST"=>array_values($white_list)];

       return response()->json($response);

   }

    public function appUsage(Request $request){
      
        //$json_file= json_decode($request->all(),true);
        //return print_r($json);
        
        try {
            $child = Child::findOrFail($request['CHILD_ID']);
           
        }
        catch (\Exception $e){
            return response()->json(['error'=>'CHILD']);
        }
        
      
        
        
        foreach ($request["ALL_INSTALLED_APPLICATIONS"] as $instaled_app){
            $used_app= Applications::where('name_of_package',$instaled_app['PACKAGE_NAME'])->where('child',$request['CHILD_ID'])->first();
            if($used_app==NULL){
                Applications::create(['child'=>$request['CHILD_ID'],'name_of_package'=>$instaled_app['PACKAGE_NAME'],'name_of_application'=>$instaled_app['APPLICATION_NAME']]);
            }
            
        }
       
        
        foreach ($request['APPLICATION_USAGE'] as $app_usage){
            $app=  Applications::where('name_of_package',$app_usage['PACKAGE_NAME'])->where('child',$request['CHILD_ID'])->first();
           // if($app==NULL){
              // $new_app= Applications::create(['name_of_package'=>$instaled_app['PACKAGE_NAME'],'name_of_application'=>$instaled_app['APPLICATION_NAME']]);
             //  UseApp::create(['application'=>$new_app['id'],'child'=>$request['CHILD_ID'],'interval'=>$app_usage['INTERVAL'],'time_of_creation'=>$instaled_app['TIME']]);
            //}
            //else{
               // $use_app=  UseApp::where('application',$app->id)->where('child',$request['CHILD_ID'])->first();
                //if($schedule_app==null)
               UseApp::create(['application'=>$app['id'],'child'=>$request['CHILD_ID'],'interval'=>$app_usage['INTERVAL'],'time_of_creation'=>$instaled_app['DAY']]);
               // else
                   // $use_app->update(['interval'=>$app_usage['INTERVAL'],'time_of_creation'=>$app_usage['TIME']]);
           // }
        }
        
        return response()->json(['MESSAGE'=>201]);
        
    }
    
    public function netUsage(Request $request){
        $request['CHILD_ID']=1;
       try {
           $child = Child::findOrFail($request['CHILD_ID']);
       }
       catch (\Exception $e){
           return response()->json(['error'=>'CHILD']);
       }

       $net_usage= ScheduleNet::where('child',$request['CHILD_ID'])->first();
       $counter=0;
       $net_list=[];
       if($net_usage==null)
           return response()->json(['MESSAGE'=>'No schedule']);
       foreach($net_usage as $net){
            $net_list[$counter] =['DAY'=>$net_usage->day,'INTERVAL'=>$net_usage->interval,'TIME'=>$net_usage->time];
           
       }
       $response=["CHILD_ID"=>$request['CHILD_ID'],"NTERNET_SCHEDULE"=>array_values($net_list)];

       return response()->json($response);

   }
    
}
