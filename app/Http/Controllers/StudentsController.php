<?php

namespace App\Http\Controllers;
use App\Models\Students;
use App\Models\ClassEnrollments;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function getStudents(Request $request) {
        $studentIds = [];
        if($request->has('options')){
            $Students = Students::select('id as value', 'name as label')->get()->toArray();
        }
        elseif($request->has('unenroll_class')){

            $studentIds = ClassEnrollments::where(['class_id'=>$request->unenroll_class])->pluck('student_id')->toArray();
            $Students = Students::whereIn('id',$studentIds)->select('id as value', 'name as label')->get()->toArray();
        }else{
            $Students = Students::get(['id','name','age']);
        }

        if ($Students) {

            $response = ['data' => $Students,'groupIds'=>$studentIds];
            return response($response, 200);

        } else {
            $response = ["message" =>'no data found'];
            return response($response, 422);
        }
    }


    public function createStudent(Request $request) {

        Students::create(['name'=>$request->name,'age'=>$request->age]);
        $Students = Students::get(['id','name','age']);

        if ($Students) {
            $response = ['data' => $Students, 'message'=>"New student added"];
            return response($response, 200);

        } else {
            $response = ["message" =>'no data found'];
            return response($response, 422);
        }
    }

    public function deleteStudent ($id) {

        $Students = Students::where("id",$id)->delete();
        $Students = ClassEnrollments::where(['student_id'=>$id])->delete();

        if ($Students) {
            $response = ['data' => $Students, 'message'=>"Student deleted"];
            return response($response, 200);

        } else {
            $response = ["message" =>'no data found'];
            return response($response, 422);
        }
    }

    public function enrollStudent(Request $request){
        try {
            $exists = ClassEnrollments::where(['class_id'=>$request->classid,'student_id'=>$request->studentid])->exists();
            if($exists){
                $response = ['data' => [], 'message'=>"Student already enrolled in this class",'success'=>'false'];
                return response($response, 200);
            }

            $count = ClassEnrollments::where(['class_id'=>$request->classid])->count();
            if($count>=4){
                $response = ['data' => [], 'message'=>"All seats are fulled",'success'=>'false'];
                return response($response, 200);
            }


            ClassEnrollments::create(['class_id'=>$request->classid,'student_id'=>$request->studentid]);
            $response = ['data' => [], 'message'=>"Student enrollment completed", 'success'=>'true'];
            return response($response, 200);
        } catch (\Exception $ex) {
            $response = ["message" =>'Something went wrong'];
            return response($response, 422);
        }

    }

    public function unenrollStudent(Request $request){
        try {
            if(empty($request->studentids)){
                $response = ['data' => [], 'message'=>"Please select atleat one selection",'success'=>'false'];
                return response($response, 200);
            }

            ClassEnrollments::where(['class_id'=>$request->classid])->whereIn('student_id', $request->studentids)->delete();
            $response = ['data' => [], 'message'=>"Selected students unenrolled from class", 'success'=>'true'];
            return response($response, 200);
        } catch (\Exception $ex) {
            $response = ["message" =>'Something went wrong'];
            return response($response, 422);
        }

    }
}
