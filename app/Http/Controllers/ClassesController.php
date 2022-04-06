<?php

namespace App\Http\Controllers;
use App\Models\Classes;
use App\Models\ClasseEnrollments;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function getClasses (Request $request) {

        try {
            $Classes = Classes::get(['id','name','seats','description']);

            if ($Classes->isNotEmpty()) {
                $data = [];
                foreach ($Classes as $key => $value) {
                    $total_enrollments = \DB::table('class_enrollments')->where("class_id",$value->id)->get()->count();
                    $value->pending_seats = $value->seats - $total_enrollments;
                    $data[$key] = $value;
                }
                $response = ['data' => $data];
                return response($response, 200);

            } else {
                $response = ["message" =>'no data found'];
                return response($response, 422);
            }
        } catch (\Exception $e) {
            $response = ["message" =>$e->getMessage()];
                return response($response, 422);
        }


    }

    public function createClass(Request $request) {

        Classes::create(['name'=>$request->name,'description'=>$request->description,'seats'=>4]);
        $Classes = Classes::get(['id','name','description','seats']);

        if ($Classes) {
            $response = ['data' => $Classes, 'message'=>"New class added"];
            return response($response, 200);

        } else {
            $response = ["message" =>'no data found'];
            return response($response, 422);
        }
    }
}
