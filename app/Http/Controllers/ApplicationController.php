<?php

namespace App\Http\Controllers;

use App\Applications;
use App\Child;
use App\ScheduleApp;
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
        try {
            $child = Child::findOrFail($request['CHILD_ID']);
        }
        catch (\Exception $e){
            return response()->json(['error'=>'CHILD']);
        }
        
    }
}
