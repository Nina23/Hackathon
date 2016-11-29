<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parents;
use Validator;
use Hash;
use App\ParentsChild;
use App\Child;
use App\ResetPass;
use Mail;
use DB;

class ParentsController extends Controller {

    public function storeParents(Request $request) {

//        return print_r($request['MAIL']);

        $rules = array(
            'MAIL' => 'email|required',
            'PASSWORD' => 'required',
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
            return response()->json(['ERROR_ID' => 15]);
        }

       
        $unique = uniqid() . '_' . uniqid();

        $parent_data = ['unique_id' => $unique,
            'email' => $request['MAIL'],
            'password' => bcrypt($request['PASSWORD']),
            'first_name' => $request['NAME'],
            'last_name' => $request['SURNAME'],
            'number' => $request['PHONE'],
            'status' => 0,
        ];


        try {
            $parent = Parents::create($parent_data);
            $response = ['SUCCESS' => true, 'ID' => $parent->id, 'UNIQUE_ID' => $parent->unique_id];
            return response()->json($response);
        } catch (\Exception $e) {
            $response = ['SUCCESS' => false, 'ERROR_ID' => 1];
            return response()->json($response);
        }
    }

    public function loginParents(Request $request) {
        $rules = array(
            'MAIL' => 'email|required',
            'PASSWORD' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }
        
        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERROR_ID' => 15]);
        }


        $parent = Parents::where('email', $request['MAIL'])->first();
        
        if($parent->activated==1){
            return response()->json(['ERROR_ID' => 14]);
        }

        if ($parent == null) {
            $response = ['SUCCESS' => false, 'ERROR_ID' => 3];
            return response()->json($response);
        }


        if (Hash::check($request['PASSWORD'], $parent['password'])) {

            $parents_child = ParentsChild::where('parents', $parent->id)->get();
            $list_children = [];
            $counter = 0;
            foreach ($parents_child as $child_id) {

                $child = Child::where('id', $child_id->child)->first();

                if ($child != null) {
                    $list_children[$counter] = ['CHILD_ID' => $child->id,
                        'UNIQUE_ID' => $child['unique_id'],
                        'PHONE' => $child['number'],
                        'ADDRESS' => $child['address'],
                        'NAME' => $child['first_name'],
                        'SURNAME' => $child['last_name'],
                        'IMAGE' => $child['image'],
                        'STATUS' => $child['status'],
                        'SEX' => $child['sex']
                    ];
                    $counter++;
                }
            }
            $response = [
                'SUCCESS' => true,
                'PARENT_ID' => $parent['id'],
                'UNIQUE_ID' => $parent['unique_id'],
                'NAME' => $parent['first_name'],
                'SURNAME' => $parent['last_name'],
                'MAIL' => $parent['email'],
                'PHONE' => $parent['number'],
                'ADDRESS' => $parent['address'],
                'IMAGE' => $parent['image'],
                'STATUS' => $parent['status'],
                'ACTIVATED' => $parent['activated'],
                'FRENDINO_PRO' => $parent['frendino_pro'],
                'CHILDREN' => array_values($list_children)];



            return response()->json($response);
        } else {
            $response = ['SUCCESS' => false, 'ERROR_ID' => 4];
            return response()->json($response);
        }
    }

    public function deactivation(Request $request) {
        $rules = array(
            'MAIL' => 'required|email',
            'PARENT_ID' => 'required',
            'ACTIVATED' => 'required',
            'TOKEN' => 'required'

        );
        // return print_r($request->all());

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }
        
        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERROR_ID' => 15]);
        }

        try {
            $parent = Parents::where('unique_id', $request['PARENT_ID'])->where('email', $request['MAIL'])->first();
        } catch (\Exception $e) {
            return response()->json(['ERROR_ID' => 13]);
        }
        if ($parent != null) {

            $parent->update(['activated' => intval($request['ACTIVATED'])]);
            $response = ['SUCCESS' => true];
            return response()->json($response);
        } else {
            return response()->json(['ERROR_ID' => 13]);
        }
    }

    public function activateFrendinoPro(Request $request) {
        $rules = array(
            'UNIQUE_ID' => 'required',
            'FRENDINO_PRO' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }
        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERROR_ID' => 15]);
        }


        $parent = Parents::where('unique_id', $request['UNIQUE_ID'])->first();
        
        if($parent->activated==1){
            return response()->json(['ERROR_ID' => 14]);
        }
        if ($parent == null) {
            $response = ['SUCCESS' => false, 'ERROR_ID' => 3];
            return response()->json($response);
        }

        $parent->update(['frendino_pro' => $request['FRENDINO_PRO']]);
        $response = ['SUCCESS' => true];
        return response()->json($response);
    }

    public function getResetPassword(Request $request) {

        $rules = array(
            'MAIL' => 'required|email',
            'TOKEN'=>'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }
        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERROR_ID' => 15]);
        }

        $parent = Parents::where('email', $request['MAIL'])->first();
        if($parent->activated==1){
            return response()->json(['ERROR_ID' => 14]);
        }

        if ($parent != null) {

            $token = str_random(64);

            ResetPass::create(['email' => $request['MAIL'], 'token' => $token]);

            $data['token'] = $token;
            $data['email'] = $request['MAIL'];

            $niz = [
                'data' => $data
            ];
            $user = ['email' => $request['MAIL']];
            Mail::send('user.resset_password', $niz, function ($m) use ($user) {
                $m->from('info@frendino.com', 'Frendino');
                $m->to($user['email'], '');
                $m->subject('Token for resset password');
            });
            $response = ['SUCCESS' => true];
            return response()->json($response);
        } else {
            $response = ['SUCCESS' => false];
            return response()->json($response);
        }
    }

    public function changePassword(Request $request) {
        $rules = array(
            'MAIL' => 'required|email',
            'TEMP_PASS' => 'required',
            'PASSWORD' => 'required',
            'TOKEN' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }
        if (config('token.token') != $request['TOKEN']) {
            return response()->json(['ERROR_ID' => 15]);
        }

        $parent = Parents::where('email', $request['MAIL'])->first();
        
        if($parent->activated==1){
            return response()->json(['ERROR_ID' => 14]);
        }

        if ($parent != null) {
            $reset_pass = ResetPass::where('token', $request['TEMP_PASS'])->where('email', $request['MAIL'])->first();
            if ($reset_pass != null) {
                $parent->update(['password' => bcrypt($request['PASSWORD'])]);
                DB::table('password_resets')->where('token', $request['TEMP_PASS'])->delete();
                $response = ['SUCCESS' => true];
                return response()->json($response);
            } else {
                $response = ['SUCCESS' => false];
                return response()->json($response);
            }
        } else {
            $response = ['ERROR_ID' => 13];
            return response()->json($response);
        }
    }

}
