<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Ramsey\Uuid\v1;

class AdminController extends Controller
{
    public function index(Request $request){
        //first check if you are loggedin and admin user...
        if(!Auth::check() && $request->path() != 'login'){
            return redirect('/login');
        }
        if(!Auth::check() && $request->path() == 'login'){
            return view('welcome');
        }
        //you are already logged in... so check for if you are an admin user...
        $user = Auth::user();
        if($user->userType=='User'){
            return redirect('/login');
        }
        if($request->path() == 'login'){
            return redirect('/');
        }
        return $this->checkForPermission($user, $request);
    }
    public function checkForPermission($user, $request){
        $permission = json_decode($user->role->permission);
        $hasPermission = false;
        foreach($permission as $p){
            if($p->name==$request->path()){
                if($p->read){
                    $hasPermission = true;
                }
            }
        }
        if($hasPermission) return view('welcome');
        return view('notfound');
    }
    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
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
            'role_id'  => 'required'
        ]);
        $password = bcrypt($request->password);
        $user = User::create([
            'fullName'  => $request->fullName,
            'email'     => $request->email,
            'password'  => $password,
            'role_id'  => $request->role_id
        ]);
        return $user;
    }

    public function getUsers(){
        return User::get();
    }

    public function editUser(Request $request){
        $this->validate($request, [
            'fullName'  => 'required',
            'email'     => "bail|required|email|unique:users,email,$request->id",
            'password'  => 'min:6',
            'role_id'  => 'required'
        ]);
        $data = [
            'fullName'  => $request->fullName,
            'email'     => $request->email,
            'role_id'  => $request->role_id
        ];
        if($request->password){
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }

        $user = User::where('id', $request->id)->update($data);
        return $user;
    }
    public function deleteUser(Request $request){
        $this->validate($request, [
            'id'      => 'required'
        ]);
        return User::where('id', $request->id)->delete();
    }
    //admin login
    public function adminLogin(Request $request){
        $this->validate($request, [
            'email'     => 'bail|required|email',
            'password'  => 'min:6|required'
        ]);
        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $user = Auth::user();
            if($user->role->isAdmin == 0){
                Auth::logout();
                return response()->json([
                    'msg'   => 'Incorrect login details'
                ], 401);
            }
            return response()->json([
                'msg'   => 'You are logged in',
                'user'  => $user
            ]);
        }else{
            return response()->json([
                'msg'   => 'Incorrect login details'
            ], 401);
        }
    }

    //role
    public function addRole(Request $request){
        $this->validate($request, [
            'roleName'     => 'required'
        ]);
        return $role = Role::create([
            'roleName' => $request->roleName
        ]);
    }
    public function getRoles(){
        return Role::all();
    }
    public function editRole(Request $request){
        $this->validate($request, [
            'roleName'     => 'required'
        ]);
        return Role::where('id', $request->id)->update([
            'roleName' => $request->roleName
        ]);
    }
    public function deleteRole(Request $request){
        $this->validate($request, [
            'id'      => 'required'
        ]);
        return Role::where('id', $request->id)->delete();
    }
    public function assignRole(Request $request){
        $this->validate($request, [
            'permission'=> 'required',
            'id'        => 'required'
        ]);
        return Role::where('id', $request->id)->update([
            'permission'=> $request->permission
        ]);
    }
}
