<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller {

    public function reciveFile(Request $request) {
        $rules = array(
            'DESCRIPTION' => 'required',
            'image' => 'required',
            'CATEGORY' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['ERROR' => "VERIFICATION"]);
        }
        
        $file = $request->file('image');
        Storage::disk('hackathon')->put($file->getClientOriginalName(), file_get_contents($file->getRealPath()));
        $name=$file->getClientOriginalName();
        
        Reports::create([
            'name'=>$name,
            'attachment'=>$request['CATEGORY'],
            'description'=>$request['DESCRIPTION'],
            'failed'=>1
            ]);
        
        return response()->json(['SUCCESS'=>"TRUE"]);

    }

}
