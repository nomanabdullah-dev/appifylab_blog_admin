<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\User;

class AdminController extends Controller
{
    //tag
    public function getTag(){
        return Tag::orderBy('id', 'desc')->get();
    }

    public function addTag(Request $request){
        $this->validate($request, [
            'tagName' => 'required'
        ]);

        return Tag::create([
            'tagName' => $request->tagName
        ]);
    }

    public function editTag(Request $request){
        $this->validate($request, [
            'id'      => 'required',
            'tagName' => 'required'
        ]);

        return Tag::where('id', $request->id)->update([
            'tagName' => $request->tagName
        ]);
    }

    public function deleteTag(Request $request){
        $this->validate($request, [
            'id'      => 'required'
        ]);

        return Tag::where('id', $request->id)->delete();
    }


    //category
    public function getCategory(){
        return Category::orderBy('id', 'desc')->get();
    }

    public function upload(Request $request){
        $this->validate($request, [
            'file'      => 'required|mimes:png,jpg,jpeg'
        ]);
        $picName = time().'.'.$request->file->extension();
        $request->file->move(public_path('uploads'), $picName);
        return $picName;
    }

    public function deleteImage(Request $request){
        $fileName = $request->imageName;
        $this->deleteFileFromServer($fileName, false);
        return 'done';
    }

    public function deleteFileFromServer($fileName, $hasFullPath = false){
        if(!$hasFullPath){
            $filePath = public_path().'/uploads/'.$fileName;
        }
        if(file_exists($filePath)){
            @unlink($filePath);
        }
        return;
    }

    public function addCategory(Request $request){
        $this->validate($request, [
            'categoryName'  => 'required',
            'iconImage'     => 'required'
        ]);
        return Category::create([
            'categoryName'  => $request->categoryName,
            'iconImage'     => $request->iconImage
        ]);
    }

    public function editCategory(Request $request){
        $this->validate($request, [
            'categoryName'  => 'required',
            'iconImage'     => 'required'
        ]);
        return Category::where('id', $request->id)->update([
            'categoryName'  => $request->categoryName,
            'iconImage'     => $request->iconImage
        ]);
    }

    public function deleteCategory(Request $request){
        //first delete the original file from the server
        $this->deleteFileFromServer($request->iconImage);
        $this->validate($request, [
            'id'      => 'required'
        ]);
        return Category::where('id', $request->id)->delete();
    }

    //admin user
    public function createUser(Request $request){
        $this->validate($request, [
            'fullName'  => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:6',
            'userType'  => 'required'
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'fullName'  => $request->fullName,
            'email'     => $request->email,
            'password'  => $password,
            'userType'  => $request->userType
        ]);
        return $user;
    }

    public function getUsers(){
        return User::where('userType', '!=', 'User')->get();
    }

    public function editUser(Request $request){
        $this->validate($request, [
            'fullName'  => 'required',
            'email'     => "bail|required|email|unique:users,email,$request->id",
            'password'  => 'min:6',
            'userType'  => 'required'
        ]);
        $data = [
            'fullName'  => $request->fullName,
            'email'     => $request->email,
            'userType'  => $request->userType
        ];
        if($request->password){
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }
        
        $user = User::where('id', $request->id)->update($data);
        return $user;
    }
}
