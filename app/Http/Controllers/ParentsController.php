<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parents;
use Validator;
use Hash;
use App\ParentsChild;
use App\Child;

class ParentsController extends Controller {

    public function storeParents(Request $request) {

//        return print_r($request['MAIL']);

        $rules = array(
            'MAIL' => 'email|required',
            'PASSWORD' => 'required',
            'NAME' => 'required',
            'SURNAME' => 'required',
            'PHONE' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
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
            'PASSWORD' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }

        $parent = Parents::where('email', $request['MAIL'])->first();

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
            'CHILD_ID' => 'required',
            'PARENT_ID' => 'required'
        );
        // return print_r($request->all());

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }

        try {
            $child = Child::findOrFail($request['CHILD_ID']);
        } catch (\Exception $e) {
            return response()->json(['ERROR_ID' => 9]);
        }


        try {
            $parent = Parents::findOrFail($request['PARENT_ID']);
        } catch (\Exception $e) {
            return response()->json(['ERROR_ID' => 13]);
        }



        $parentChild = ParentsChild::where('child', $request['CHILD_ID'])->where('parents', $request['PARENT_ID'])->first();
        if ($parentChild != null) {
            ParentsChild::destroy($parentChild->id);
            Parents::destroy($parent->id);
            Child::destroy($child->id);
            $response = ['SUCCESS' => true];
            return response()->json($response);
        } else {
            return response()->json(['ERROR_ID' => 13]);
        }
    }

    public function activateFrendinoPro(Request $request) {
        $rules = array(
            'UNIQUE_ID' => 'required',
            'FRENDINO_PRO' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR_ID' => 8]);
        }


        $parent = Parents::where('unique_id', $request['UNIQUE_ID'])->first();
        if ($parent == null) {
            $response = ['SUCCESS' => false, 'ERROR_ID' => 3];
            return response()->json($response);
        }

        $parent->update([['frendino_pro' => $request['FRENDINO_PRO']]]);
        $response = ['SUCCESS' => true];
        return response()->json($response);
    }

}
