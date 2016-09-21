<?php

namespace App\Http\Controllers;

use App\Applications;
use App\Child;
use App\UseApp;
use App\ScheduleNet;
use App\ScheduleApp;
use App\ScheduleChild;
use Illuminate\Http\Request;
use App\Location;
use App\Parents;
use App\ParentsChild;
use Validator;



class ApplicationController extends Controller
{

    /*
     * Api for return black and white list and use application from white list
     * */


   public function blackWhiteList(Request $request){
        $rules = array(
            'CHILD_ID'=>'required'
           
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
       
       try {
           $child = Child::findOrFail($request['CHILD_ID']);
       }
       catch (\Exception $e){
           return response()->json(['ERROR_ID'=>9]);
       }

       $appliactions=Applications::where('child',$request['CHILD_ID'])->get();
       
     

       $black_list=[];
       $white_list=[];
       $counter_white=0;
       $counter_black=0;

       foreach($appliactions as $app){
           if(1==$app->status) {
               $schedule_apps=ScheduleApp::where('application',$app->id)->get();
                $schedule_list=[];
                $counter_schedule=0;
               foreach ($schedule_apps as $schedule_app){
                  $schedule_list[$counter_schedule]= ['DAY'=>$schedule_app->day,'INTERVAL'=>$schedule_app->interval,'TIME'=>$schedule_app->time];
              
                   $counter_schedule++;
              
                   // $white_list[$counter_white] = ['PACKAGE_NAME' => $app->name_of_package, 'APPLICATION_NAME'=>$app->name_of_application];
               }
               $white_list[$counter_white]=['PACKAGE_NAME' => $app->name_of_package,'APPLICATION_NAME'=>$app->name_of_application,'APLICATION_SCHEDULE'=>$schedule_list];
               $counter_white++;      
           }
           if(2==$app->status){
               $black_list[$counter_black]=['PACKAGE_NAME'=>$app->name_of_package,'APPLICATION_NAME'=>$app->name_of_application];
               $counter_black++;
           }
       }
       
       $response=["CHILD_ID"=>$request['CHILD_ID'],"APP_BLACKLIST"=>array_values($black_list),"APP_WHITELIST"=>array_values($white_list)];
       return response()->json($response);

   }

    public function appUsage(Request $request){
        
         $rules = array(
            'CHILD_ID'=>'required'
           
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
        
      
        //$json_file= json_decode($request->all(),true);
        //return print_r($json);
        
        try {
            $child = Child::findOrFail($request['CHILD_ID']);
           
        }
        catch (\Exception $e){
            return response()->json(['ERROR_ID'=>9]);
        }
        
        foreach ($request["ALL_INSTALLED_APPLICATIONS"] as $instaled_app){
            $used_app= Applications::where('name_of_package',$instaled_app['PACKAGE_NAME'])->where('child',$request['CHILD_ID'])->first();
            
            if($used_app==""){
                // return response()->json(['MESSAGE'=>'ovde si']);
                Applications::create(['child'=>$request['CHILD_ID'],'name_of_package'=>$instaled_app['PACKAGE_NAME'],'name_of_application'=>$instaled_app['APPLICATION_NAME']]);
            }
            
        }
       
        foreach ($request['APPLICATION_USAGE'] as $app_usage){
            
            $app=  Applications::where('name_of_package',$app_usage['PACKAGE_NAME'])->where('child',$request['CHILD_ID'])->first();
            if($app==NULL){
                
               $new_app= Applications::create(['child'=>$request['CHILD_ID'],'name_of_package'=>$app_usage['PACKAGE_NAME'],'name_of_application'=>$app_usage['APPLICATION_NAME']]);
               UseApp::create(['application'=>$new_app['id'],'child'=>$request['CHILD_ID'],'interval'=>$app_usage['INTERVAL'],'time_of_creation'=>$app_usage['DAY']]);
            }
            else{
               // $use_app=  UseApp::where('application',$app->id)->where('child',$request['CHILD_ID'])->first();
                //if($schedule_app==null)
               UseApp::create(['application'=>$app['id'],'child'=>$request['CHILD_ID'],'interval'=>$app_usage['INTERVAL'],'time_of_creation'=>$app_usage['DAY']]);
               // else
                   // $use_app->update(['interval'=>$app_usage['INTERVAL'],'time_of_creation'=>$app_usage['TIME']]);
            }
        }
        
        return response()->json(['SUCCESS'=>true]);
        
    }
    
    public function netUsage(Request $request){
         $rules = array(
            'CHILD_ID'=>'required'
           
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
        
        
        
       try {
           $child = Child::findOrFail($request['CHILD_ID']);
       }
       catch (\Exception $e){
           return response()->json(['ERROR_ID'=>9]);
       }

       $net_usage= ScheduleNet::where('child',$request['CHILD_ID'])->get();
       $counter=0;
       $net_list=[];
       if($net_usage==null)
           return response()->json(['MESSAGE'=>'No schedule']);
       
       foreach($net_usage as $net){
            $net_list[$counter] =['DAY'=>$net->day,'INTERVAL'=>$net->interval,'TIME'=>$net->time];
            $counter++;
           
       }
       $response=["CHILD_ID"=>$request['CHILD_ID'],"INTERNET_SCHEDULE"=>array_values($net_list)];

       return response()->json($response);

   }
   
   
   
    public function childSchedule(Request $request){
        $rules = array(
            'CHILD_ID'=>'required'   
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
        
        
        try {
           $child = Child::findOrFail($request['CHILD_ID']);
       }
       catch (\Exception $e){
           return response()->json(['ERROR_ID'=>9]);
       }
         
       
       
       $shedule_child= ScheduleChild::where('child',$request['CHILD_ID'])->get();
      
       $event_list=[];
       $counter=0;
       foreach($shedule_child as $shedule){
           $event_list[$counter]=['EVENT_ID'=>$shedule['id'],'TIME'=>$shedule['time'],'EVENT'=>$shedule['note']];
           $counter++;
       }
       $response=["CHILD_ID"=>$request['CHILD_ID'],"EVENTS_SCHEDULE"=>  array_values($event_list)];
         return response()->json($response);
       
   }
   
   public function saveLocation(Request $request){
       $rules = array(
            'CHILD_ID'=>'required'   
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
       
       
       try {
           $child = Child::findOrFail($request['CHILD_ID']);
       }
       catch (\Exception $e){
           return response()->json(['ERROR_ID'=>9]);
       }
       
      
       
       
       foreach($request['LOCATIONS'] as $location){
           if(!array_key_exists('SPEED',$location))
                    $location['SPEED']="";
           if(!array_key_exists('STATE',$location))
                    $location['STATE']="";
           if(!array_key_exists('ACCURACY',$location))
                    $location['ACCURACY']="";
           if(!array_key_exists('PROVIDER',$location))
                    $location['PROVIDER']="";
            
           Location::create(['child'=>$request['CHILD_ID'],'lang'=>$location['LANG'],'lat'=>$location['LAT'],'time_of_location'=>$location['TIME'],
               'speed'=>$location['SPEED'],
               'state'=>$location['STATE'],
               'accuracy'=>$location['ACCURACY'],
               'provider'=>$location['PROVIDER']
                ]);
       }
       
       $response=['SUCCESS'=>true];
        return response()->json($response);
   }
   public function saveEvent(Request $request){
       $rules = array(
            'CHILD_ID'=>'required'   
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
       
       try {
           $child = Child::findOrFail($request['CHILD_ID']);
       }
       catch (\Exception $e){
           return response()->json(['ERROR_ID'=>9]);
       }
       if($request['ACTION']==1){
           ScheduleChild::create(['child'=>$request['CHILD_ID'],'time'=>$request['TIME'],'note'=>$request['EVENT']]);
       }
       elseif($request['ACTION']==2){
       $shedule_child= ScheduleChild::where('id',$request['EVENT_ID'])->where('child',$request['CHILD_ID'])->first();
       $shedule_child->update(['time'=>$request['TIME'],'note'=>$request['EVENT']]);
       }
       elseif($request['ACTION']==3){
           $shedule_child= ScheduleChild::where('id',$request['EVENT_ID'])->where('child',$request['CHILD_ID'])->first();
           $shedule_child->delete();
       }
       
       else{
           $response=['ACTION'=>'0'];
        return response()->json($response);
       }
       $response=['SUCCESS'=>true];
       return response()->json($response);
       
   }
   
   public function allInstaledApp(Request $request){
       $rules = array(
            'CHILD_ID'=>'required'   
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
       try {
            $child = Child::findOrFail($request['CHILD_ID']);
           
        }
        catch (\Exception $e){
            return response()->json(['ERROR_ID'=>9]);
        }
        $applications = Applications::where('child',$request['CHILD_ID'])->get();
        $app_list=[];
        $counter_all_app=0;
        foreach ($applications as $app){
           
            $counter=0;
            $schedule_list=[];
            $schedule_apps=  ScheduleApp::where('application',$app->id)->get();
            if($schedule_apps!=null){
            foreach($schedule_apps as $schedule_app){
                $schedule_list[$counter]=['SCHEDULE_ID'=>$schedule_app->id,'DAY'=>$schedule_app->day,'TIME'=>$schedule_app->time,'INTERVAL'=>$schedule_app->interval];
                $counter++;
            }
            }
            
            
            $use_app_list=[];
            $counter_app=0;
            $use_app=UseApp::where('child',$request['CHILD_ID'])->where('application',$app->id)->get();
            //return print_r($use_app);
            if($use_app!=NULL){
                foreach($use_app as $app_day){
                $use_app_list[$counter_app]=['TIME_OF_CREATION'=>$app_day->time_of_creation,'INTERVAL'=>$app_day->interval];
                $counter_app++;
                }
            }
             
            if($app!=null)
            {
                $app_list[$counter_all_app]=['APPLICATION_NAME'=>$app->name_of_application,'APPLICATION_ID'=>$app->id,'APPLICATION_STATUS'=>$app->status,'APPLICATION_SCHEDULE'=>array_values($schedule_list),'APPLICATION_USAGE'=>array_values($use_app_list)];
            $counter_all_app++;
            }
            
        }
        
         $response=["APPLICATION_LISTS"=>  array_values($app_list)];
         return response()->json($response);
       
        
   }
   
   public function changeStatusApp(Request $request){
       $rules = array(
            'CHILD_ID'=>'required'   
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
       
       
       try {
            $child = Child::findOrFail($request['CHILD_ID']);
           
        }
        catch (\Exception $e){
            return response()->json(['ERROR_ID'=>9]);
        }
        
        
        
        $app=Applications::where('child',$request['CHILD_ID'])->where('id',$request['APPLICATION_ID'])->first();
        
        if($app!=null){
           
            $app->update(['status'=>$request['APPLICATIONS_STATUS']]);
        }
        else{
            return response()->json(['ERROR_ID'=>10]);
        }
        
        
        $response=['APPLICATION_STATUS'=>$app['status']];
       return response()->json($response);
      
   }
   
   public function changeScheduleApp(Request $request){
        $rules = array(
            'CHILD_ID'=>'required'   
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
       
       try {
            $child = Child::findOrFail($request['CHILD_ID']);
           
        }
        catch (\Exception $e){
            return response()->json(['ERROR_ID'=>9]);
        }
        
        $schedule_app= ScheduleApp::where('id',$request['SCHEDULE_ID'])->where('application',$request['APPLICATION'])->first();
        if($schedule_app!=null){
            $schedule_app->update(['day'=>$request['DAY'],'time'=>$request['TIME'],'interval'=>$request['INTERVAL']]);
        }
         else{
            return response()->json(['error'=>'Schedule does not exists']);
        }
        
        $response=['SCHEDULE_ID'=>$schedule_app['id'],'DAY'=>$schedule_app['day'],'INTERVAL'=>$schedule_app['interval'],'TIME'=>$schedule_app['time']];
       return response()->json($response);
   }
   
   public function createScheduleApp(Request $request){
        $rules = array(
            'CHILD_ID'=>'required'   
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
       
       
       try {
            $child = Child::findOrFail($request['CHILD_ID']);
           
        }
        catch (\Exception $e){
            return response()->json(['ERROR_ID'=>9]);
        }
         //return response()->json(['nina'=>true]);
     
        $schedule_app=ScheduleApp::create(['child'=>$request['CHILD_ID'],'application'=>$request['APPLICATION_ID'],'day'=>$request['DAY'],'time'=>$request['TIME'],'interval'=>$request['INTERVAL']]);
       
        $response=['APPLICATION_ID'=>$schedule_app['application'],'SCHEDULE_ID'=>$schedule_app['id'],'DAY'=>$schedule_app['day'],'INTERVAL'=>$schedule_app['interval'],'TIME'=>$schedule_app['time']];
       return response()->json($response);
        
        }
        
        public function allLocationsChild(Request $request){
             $rules = array(
            'CHILD_ID'=>'required'   
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
            try {
            $child = Child::findOrFail($request['CHILD_ID']);
           
        }
        catch (\Exception $e){
            return response()->json(['ERROR_ID'=>9]);
        }
        $locations=  Location::where('child',$request['CHILD_ID'])->orderBy('time_of_location', 'desc')->get();
        $counter=0;
        $locations_list=[];
        foreach($locations as $location){
            $locations_list[$counter]=['LAT'=>$location['lat'],'LNG'=>$location['lang'],'TIME'=>$location['time_of_location']];
            $counter++;
        }
        $response=['CHILD_ID'=>$request['CHILD_ID'],'LOCATIONS'=>  array_values($locations_list)];
        
        return response()->json($response);
        
        }
        
        public function storeChild(Request $request){
            $rules = array(
            'CHILD_PHONE'=>'required',
            'NAME'=>'required',
            'SURNAME'=>'required',
            'PHONE'=>'required'
        );
            
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
        
        $parents=Parents::all();
        $contition=0;
        foreach($parents as $parent){
            
            if(strcmp($parent['number'], $request['PHONE'])===0){
                
                $contition=1;
                $unique =  uniqid().'_'.uniqid();
                $child_data=['unique_id'=>  $unique,
                            'first_name'=>$request['NAME'],
                            'last_name'=>$request['SURNAME'],
                            'number'=>$request['CHILD_PHONE']];
                
                try{
                    
                    $child= Child::create($child_data);
                    
                    $parent_find=ParentsChild::where('parents',$parent->id)->first();
                    
                    if($parent_find==""){
                       
                    $parentChild_data=['parents'=>$parent->id,
                                       'child'=>$child->id];
                    try{
                        $parentsChild=  ParentsChild::create($parentChild_data);
                        $response=['SUCCESS'=>true,'CHILD_ID'=>$child->id,'UNIQUE_ID'=>$child->unique_id];
                        return response()->json($response);
                    }
                    
                    catch (\Exception $e) {
                    $response=['SUCCESS'=>false, 'ERROR_ID'=>12];
                    return response()->json($response);
                        }
                    }
                    else{
                        $response=['SUCCESS'=>false,'ERROR_ID'=>7];
                        return response()->json($response);
                    }
                        
                        
                        
                    }
                catch (\Exception $e) {
                    $response=['SUCCESS'=>false,'ERROR_ID'=>5];
                    return response()->json($response);
                    }
            } 
        }
        
        if($contition==0){
                $response=['SUCCESS'=>false, 'ERROR_ID'=>6];
                return response()->json($response);
        }
      }
      
      
      public function deleteScheduleApp(Request $request){
           $rules = array(
            'CHILD_ID'=>'required',
            'APPLICATION'=>'required',
            'SCHEDULE_ID'=>'required'
            
        );
           
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json(['ERROR_ID'=>8]);
        }
          
          try {
            $child = Child::findOrFail($request['CHILD_ID']);  
        }
        
         catch (\Exception $e){
            return response()->json(['ERROR_ID'=>9]);
        }
        
        
        try {
            $application = Applications::where('id',$request['APPLICATION'])->where('child',$request['CHILD_ID'])->first();
        }
        
         catch (\Exception $e){
            return response()->json(['ERROR_ID'=>10]);
        }
        
        
        
        $schedule_app=  ScheduleApp::where('id',$request['SCHEDULE_ID'])->where('application',$request['APPLICATION'])->first();
         try {
            ScheduleApp::destroy($schedule_app->id);
            $response=['SUCCESS'=>true];
            return response()->json($response);
         }
        
         catch (\Exception $e){
            return response()->json(['SUCCESS'=>false, 'ERROR_ID'=>11]);
        }
        
      }
        
    
}
