<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Reports;

class ReportsController extends Controller
{
    
     public function displayFile($fileName)
    {

        $path = storage_path() . '/app/' . $fileName;
        $headers=[ 'Content-Type' => "video/mp4",
            'Content-Disposition' => 'inline; filename="'.$fileName.'"'];
        return response()->download($path,'nina',$headers);

        //return$response;
    }
    
    
    
    public static function returnAll(){
        $reports=Reports::all();
        
        
    }
}
