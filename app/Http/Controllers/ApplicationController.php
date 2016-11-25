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
use App\SchoolSettings;
use Validator;

class ApplicationController extends Controller {
    /*
     * Api for return black and white list and use application from white list
     * */

    public function blackWhiteList(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }

        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }

        $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }

        $appliactions = Applications::where('child', $child->id)->get();


        $black_list = [];
        $white_list = [];
        $counter_white = 0;
        $counter_black = 0;

        foreach ($appliactions as $app) {
            if (1 == $app->status) {
                $schedule_apps = ScheduleApp::where('application', $app->id)->get();
                $schedule_list = [];
                $counter_schedule = 0;
                foreach ($schedule_apps as $schedule_app) {
                    $schedule_list[$counter_schedule] = ['DAY' => $schedule_app->day, 'INTERVAL' => $schedule_app->interval, 'TIME' => $schedule_app->time];

                    $counter_schedule++;

                    // $white_list[$counter_white] = ['PACKAGE_NAME' => $app->name_of_package, 'APPLICATION_NAME'=>$app->name_of_application];
                }
                $white_list[$counter_white] = ['PACKAGE_NAME' => $app->name_of_package, 'APPLICATION_NAME' => $app->name_of_application, 'APLICATION_SCHEDULE' => $schedule_list];
                $counter_white++;
            }
            if (2 == $app->status) {
                $black_list[$counter_black] = ['PACKAGE_NAME' => $app->name_of_package, 'APPLICATION_NAME' => $app->name_of_application];
                $counter_black++;
            }
        }

        $response = ["CHILD_ID" => $child->id, "APP_BLACKLIST" => array_values($black_list), "APP_WHITELIST" => array_values($white_list)];
        return response()->json($response);
    }

    public function appUsage(Request $request) {

        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }


        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }


        $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }


        foreach ($request["ALL_INSTALLED_APPLICATIONS"] as $instaled_app) {
            $used_app = Applications::where('name_of_package', $instaled_app['PACKAGE_NAME'])->where('child', $child->id)->first();

            if ($used_app == "") {
                // return response()->json(['MESSAGE'=>'ovde si']);
                Applications::create(['child' => $child->id, 'name_of_package' => $instaled_app['PACKAGE_NAME'], 'name_of_application' => $instaled_app['APPLICATION_NAME'], 'status' => 1]);
            }
        }

        foreach ($request['APPLICATION_USAGE'] as $app_usage) {

            $app = Applications::where('name_of_package', $app_usage['PACKAGE_NAME'])->where('child', $child->id)->first();
            if ($app == NULL) {

                $new_app = Applications::create(['child' => $child->id, 'name_of_package' => $app_usage['PACKAGE_NAME'], 'name_of_application' => $app_usage['APPLICATION_NAME'], 'status' => 1]);
                UseApp::create(['application' => $new_app['id'], 'child' => $child->id, 'interval' => $app_usage['INTERVAL'], 'time_of_creation' => $app_usage['DAY']]);
            } else {

                UseApp::create(['application' => $app['id'], 'child' => $child->id, 'interval' => $app_usage['INTERVAL'], 'time_of_creation' => $app_usage['DAY']]);
            }
        }

        return response()->json(['SUCCESS' => true]);
    }

    public function netUsage(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }



        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }

        $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }


        $net_usage = ScheduleNet::where('child', $child->id)->get();
        $counter = 0;
        $net_list = [];
        if ($net_usage == null)
            return response()->json(['MESSAGE' => 'No schedule']);

        foreach ($net_usage as $net) {
            $net_list[$counter] = ['DAY' => $net->day, 'INTERVAL' => $net->interval, 'TIME' => $net->time];
            $counter++;
        }
        $response = ["CHILD_ID" => $child->id, "INTERNET_SCHEDULE" => array_values($net_list)];

        return response()->json($response);
    }

    public function childSchedule(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }


        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }



        $shedule_child = ScheduleChild::where('child', $child->id)->get();

        $event_list = [];
        $counter = 0;
        foreach ($shedule_child as $shedule) {
            $event_list[$counter] = ['EVENT_ID' => $shedule['id'],
                'TIME' => $shedule['time'],
                'EVENT' => $shedule['note'],
                'END_TIME' => $shedule['end_time'],
                'EVENT_TYPE' => $shedule['event_type'],
                'NOTIFICATION_TYPE' => $shedule['notification_type'],
                'NOTIFICATION_TIME' => $shedule['notification_time'],
                'EVENT_SHIFT' => $shedule['event_shift'],
                'EVENT_REPEAT' => $shedule['event_repeat'],
                'EVENT_ALL_DAY' => $shedule['event_all_day'],
            ];
            $counter++;
        }
        $school_settings = SchoolSettings::where('child', $child->id)->first();

        if ($school_settings == null) {
            $school_settings['school_state'] = "";
            $school_settings['week_switch'] = "";
        }
        $response = ["CHILD_ID" => $child->id,
            "SCHOOL_STATE" => $school_settings['school_state'],
            "WEEK_SWITCH" => $school_settings['week_switch'],
            "EVENTS_SCHEDULE" => array_values($event_list)];
        return response()->json($response);
    }

    public function saveLocation(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }


        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }



        foreach ($request['LOCATIONS'] as $location) {
            if (!array_key_exists('SPEED', $location))
                $location['SPEED'] = "";
            if (!array_key_exists('STATE', $location))
                $location['STATE'] = "";
            if (!array_key_exists('ACCURACY', $location))
                $location['ACCURACY'] = "";
            if (!array_key_exists('PROVIDER', $location))
                $location['PROVIDER'] = "";

            Location::create(['child' => $child->id, 'lang' => $location['LANG'], 'lat' => $location['LAT'], 'time_of_location' => $location['TIME'],
                'speed' => $location['SPEED'],
                'state' => $location['STATE'],
                'accuracy' => $location['ACCURACY'],
                'provider' => $location['PROVIDER']
            ]);
        }

        $response = ['SUCCESS' => true];
        return response()->json($response);
    }

    public function saveEvent(Request $request) {

        //return print_r($request->all());
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }

        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
        
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }



        if (!array_key_exists('NOTIFICATION_TYPE', $request->all())) {

            $request['NOTIFICATION_TYPE'] = 1;
        }

        if (!array_key_exists('NOTIFICATION_TIME', $request->all()))
            $request['NOTIFICATION_TIME'] = 30;

        if (!array_key_exists('EVENT_SHIFT', $request->all()))
            $request['EVENT_SHIFT'] = 0;

        if (!array_key_exists('EVENT_REPEAT', $request->all()))
            $request['EVENT_REPEAT'] = 1;

        if (!array_key_exists('EVENT_ALL_DAY', $request->all()))
            $request['EVENT_ALL_DAY'] = false;

        if ($request['ACTION'] == 1) {
            ScheduleChild::create(['child' => $child->id,
                'time' => $request['TIME'],
                'note' => $request['EVENT'],
                'end_time' => $request['END_TIME'],
                'event_type' => $request['EVENT_TYPE'],
                'notification_type' => $request['NOTIFICATION_TYPE'],
                'notification_time' => $request['NOTIFICATION_TIME'],
                'event_shift' => $request['EVENT_SHIFT'],
                'event_repeat' => $request['EVENT_REPEAT'],
                'event_all_day' => $request['EVENT_ALL_DAY']
            ]);
        } elseif ($request['ACTION'] == 2) {
            $shedule_child = ScheduleChild::where('id', $request['EVENT_ID'])->where('child', $child->id)->first();
            $shedule_child->update(['time' => $request['TIME'],
                'note' => $request['EVENT'],
                'end_time' => $request['END_TIME'],
                'event_type' => $request['EVENT_TYPE'],
                'notification_type' => $request['NOTIFICATION_TYPE'],
                'notification_time' => $request['NOTIFICATION_TIME'],
                'event_shift' => $request['EVENT_SHIFT'],
                'event_repeat' => $request['EVENT_REPEAT'],
                'event_all_day' => $request['EVENT_ALL_DAY']
            ]);
        } elseif ($request['ACTION'] == 3) {
            $shedule_child = ScheduleChild::where('id', $request['EVENT_ID'])->where('child', $child->id)->first();
            $shedule_child->delete();
        } else {
            $response = ['ACTION' => '0'];
            return response()->json($response);
        }
        $response = ['SUCCESS' => true];
        return response()->json($response);
    }

    public function allInstaledApp(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }
        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }
        
        
        $applications = Applications::where('child', $child->id)->get();
        $app_list = [];
        $counter_all_app = 0;
        foreach ($applications as $app) {

            $counter = 0;
            $schedule_list = [];
            $schedule_apps = ScheduleApp::where('application', $app->id)->get();
            if ($schedule_apps != null) {
                foreach ($schedule_apps as $schedule_app) {
                    $schedule_list[$counter] = ['SCHEDULE_ID' => $schedule_app->id, 'DAY' => $schedule_app->day, 'TIME' => $schedule_app->time, 'INTERVAL' => $schedule_app->interval];
                    $counter++;
                }
            }


            $use_app_list = [];
            $counter_app = 0;
            $use_app = UseApp::where('child', $child->id)->where('application', $app->id)->orderBy('time_of_creation', 'desc')->get();
            //return print_r($use_app);
            if ($use_app != NULL) {
                foreach ($use_app as $app_day) {
                    $use_app_list[$counter_app] = ['TIME_OF_CREATION' => $app_day->time_of_creation, 'INTERVAL' => $app_day->interval];
                    $counter_app++;
                }
            }

            if ($app != null) {
                $app_list[$counter_all_app] = ['APPLICATION_NAME' => $app->name_of_application, 'APPLICATION_ID' => $app->id, 'APPLICATION_STATUS' => $app->status, 'APPLICATION_SCHEDULE' => array_values($schedule_list), 'APPLICATION_USAGE' => array_values($use_app_list)];
                $counter_all_app++;
            }
        }

        $response = ["APPLICATION_LISTS" => array_values($app_list)];
        return response()->json($response);
    }

    public function changeStatusApp(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }


        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }



        $app = Applications::where('child', $child->id)->where('id', $request['APPLICATION_ID'])->first();

        if ($app != null) {

            $app->update(['status' => $request['APPLICATIONS_STATUS']]);
        } else {
            return response()->json(['ERROR_ID' => 10]);
        }


        $response = ['APPLICATION_STATUS' => $app['status']];
        return response()->json($response);
    }

    public function changeScheduleApp(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }

        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }

        $schedule_app = ScheduleApp::where('id', $request['SCHEDULE_ID'])->where('application', $request['APPLICATION'])->first();
        if ($schedule_app != null) {
            $schedule_app->update(['day' => $request['DAY'], 'time' => $request['TIME'], 'interval' => $request['INTERVAL']]);
        } else {
            return response()->json(['error' => 'Schedule does not exists']);
        }

        $response = ['SCHEDULE_ID' => $schedule_app['id'], 'DAY' => $schedule_app['day'], 'INTERVAL' => $schedule_app['interval'], 'TIME' => $schedule_app['time']];
        return response()->json($response);
    }

    public function createScheduleApp(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }


        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }
        

        $schedule_app = ScheduleApp::create(['child' => $child->id, 'application' => $request['APPLICATION_ID'], 'day' => $request['DAY'], 'time' => $request['TIME'], 'interval' => $request['INTERVAL']]);

        $response = ['APPLICATION_ID' => $schedule_app['application'], 'SCHEDULE_ID' => $schedule_app['id'], 'DAY' => $schedule_app['day'], 'INTERVAL' => $schedule_app['interval'], 'TIME' => $schedule_app['time']];
        return response()->json($response);
    }

    public function allLocationsChild(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }
        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
        $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }
        $locations = Location::where('child', $child->id)->orderBy('time_of_location', 'desc')->get();
        $counter = 0;
        $locations_list = [];
        foreach ($locations as $location) {
            $locations_list[$counter] = ['LAT' => $location['lat'], 'LNG' => $location['lang'], 'TIME' => $location['time_of_location']];
            $counter++;
        }
        $response = ['CHILD_ID' => $request['CHILD_ID'], 'LOCATIONS' => array_values($locations_list)];

        return response()->json($response);
    }

    public function storeChild(Request $request) {
        $rules = array(
            'CHILD_PHONE' => 'required',
            'NAME' => 'required',
            'SURNAME' => 'required',
            'PHONE' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }

        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }


        $parents = Parents::all();
        $contition = 0;
        foreach ($parents as $parent) {

            if (strcmp($parent['number'], $request['PHONE']) === 0) {
                if($parent->activated==1){
                        return response()->json(['ERORR_ID' => 14]);
                    }

                $contition = 1;
                $unique = uniqid() . '_' . uniqid();
                $child_data = ['unique_id' => $unique,
                    'first_name' => $request['NAME'],
                    'last_name' => $request['SURNAME'],
                    'number' => $request['CHILD_PHONE']];
                try {
                    $child = Child::create($child_data);

                    $parent_find = ParentsChild::where('parents', $parent->id)->first();
                    
                    if ($parent_find == "") {

                        $parentChild_data = ['parents' => $parent->id,
                            'child' => $child->id];
                        try {
                            $parentsChild = ParentsChild::create($parentChild_data);
                            $child_response = [
                                'CHILD_ID' => $child->id,
                                'UNIQUE_ID' => $child->unique_id,
                                'PHONE' => $child->number,
                                'ADDRESS' => $child->address,
                                'NAME' => $child->first_name,
                                'SURNAME' => $child->last_name,
                                'IMAGE' => $child->image,
                                'STATUS' => $child->status,
                                'SEX' => $child->sex
                            ];
                            $parent_response = [
                                'PARENT_ID' => $parent->id,
                                'UNIQUE_ID' => $parent->unique_id,
                                'NAME' => $parent->first_name,
                                'SURNAME' => $parent->last_name,
                                'MAIL' => $parent['email'],
                                'PHONE' => $parent['number'],
                                'ADDRESS' => $parent['address'],
                                'IMAGE' => $parent['image'],
                                'STATUS' => $parent['status'],
                                'ACTIVATED' => $parent['activated'],
                                'PASSWORD'=>$parent['password'],
                                'FRENDINO_PRO' => $parent['frendino_pro'],
                            ];
                            return response()->json(['SUCCESS' => true, 'CHILD' => $child_response, 'PARENT' => $parent_response]);
                        } catch (\Exception $e) {
                            $response = ['SUCCESS' => false, 'ERROR_ID' => 12];
                            return response()->json($response);
                        }
                    } else {
                        $response = ['SUCCESS' => false, 'ERROR_ID' => 7];
                        return response()->json($response);
                    }
                } catch (\Exception $e) {
                    $response = ['SUCCESS' => false, 'ERROR_ID' => 5];
                    return response()->json($response);
                }
            }
        }

        if ($contition == 0) {
            $response = ['SUCCESS' => false, 'ERROR_ID' => 6];
            return response()->json($response);
        }
    }

    public function deleteScheduleApp(Request $request) {
        $rules = array(
            'CHILD_ID' => 'required',
            'APPLICATION' => 'required',
            'SCHEDULE_ID' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }

        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }


        try {
            $application = Applications::where('id', $request['APPLICATION'])->where('child', $child->id)->first();
        } catch (\Exception $e) {
            return response()->json(['ERROR_ID' => 10]);
        }



        $schedule_app = ScheduleApp::where('id', $request['SCHEDULE_ID'])->where('application', $request['APPLICATION'])->first();
        try {
            ScheduleApp::destroy($schedule_app->id);
            $response = ['SUCCESS' => true];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['SUCCESS' => false, 'ERROR_ID' => 11]);
        }
    }

    public function createSchoolSettings(Request $request) {

        $rules = array(
            'CHILD_ID' => 'required',
            'SCHOOL_STATE' => 'required',
            'WEEK_SWITCH' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }

        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERORR_ID' => 15]);
        }

        $child = Child::where('unique_id', $request['CHILD_ID'])->first();
        if ($child == null) {
            return response()->json(['ERROR_ID' => 9]);
        }
        
         $parentsChild = ParentsChild::where('child', $child->id)->first();
        if ($parentsChild == null) {
            return response()->json(['ERROR_ID' => 12]);
        }

        $parent = Parents::where('id', $parentsChild->parents)->first();

        if ($parent == null) {
            return response()->json(['ERROR_ID' => 13]);
        }

        if ($parent->activated == 1) {
            return response()->json(['ERROR_ID' => 14]);
        }

        $school_settings = SchoolSettings::where('child', $child->id)->first();

        if ($school_settings != null) {

            try {
                $school_settings->update(['school_state' => $request['SCHOOL_STATE'], 'week_switch' => $request['WEEK_SWITCH']]);
                $response = ['SUCCESS' => true];
                return response()->json($response);
            } catch (\Exception $e) {
                $response = ['SUCCESS' => false];
                return response()->json($response);
            }
        } else {
            try {
                SchoolSettings::create(['child' => $child->id, 'school_state' => $request['SCHOOL_STATE'], 'week_switch' => $request['WEEK_SWITCH']]);
                $response = ['SUCCESS' => true];
                return response()->json($response);
            } catch (\Exception $e) {
                $response = ['SUCCESS' => false];
                return response()->json($response);
            }
        }
    }

}
