<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function createSubject(Request $request){
        $admin = User::where('cookie',$request->cookie)->first();
        if ($admin) {
            if ($admin->role >= 2) {
                $newSubject = new Subject();
                $newSubject->name = $request->name;
                $newSubject->semester = $request->semester;
                $career = Career::find($request->idCareer);
                if($career){
                    $newSubject->idCareer = $request->idCareer;
                    $newSubject->save();
                    $success = true;
                }else{
                    $success = false;
                }
            } else {
                $success = false;
            }
        } else {
            $success = false;
        }
        return response()->json([
            'success' => $success
        ]);
    }

    public function getSubjects(){
        $allSubjects = Subject::all();
        return response()->json($allSubjects->toArray());
    }

    public function getSubjectById($idSubject){
        $subject = Subject::find($idSubject);
        if ($subject) {
            return response()->json($subject->toArray());
        } else {
            $success = false;
            return response()->json(['success' => $success]);
        }
    }

    public function searchSubject(Request $request, $name){
        $subject = Subject::where('name', 'LIKE', "%$name%")->get();
        return response()->json($subject->toArray());
    }

    public function deleteSubject(Request $request, $id){
        $admin = User::where('cookie', $request->cookie)->first();
        if ($admin) {
            if (($admin->role) >=  2){
                $subject = Subject::find($id);
                if ($subject) {
                    $subject->delete();
                    $success = true;
                }else{
                    $success = false;
                }
            } else {
                $success = false;
            }
            
        } else {
            $success = false;
        }
        return response()->json([
            'success' => $success
        ]);
    }

    public function editSubject(Request $request, $id){
        $admin = User::where('cookie', $request->cookie)->first();
        if ($admin) {
            if (($admin->role) >=  2){
                $subject = Subject::find($id);
                if ($subject) {
                    $subject->name = $request->name;
                    $subject->semester = $request->semester;
                    $subject->idCareer = $request->idCareer;
                    $subject->save();
                    $success = true;
                }else{
                    $success = false;
                }
            } else {
                $success = false;
            }
            
        } else {
            $success = false;
        }
        return response()->json([
            'success' => $success
        ]);
    }
}
