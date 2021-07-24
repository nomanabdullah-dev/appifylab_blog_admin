<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Tag;

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
        $this->deleteFileFromServer($fileName);
        return 'done';
    }

    public function deleteFileFromServer($fileName){
        $filePath = public_path().'/uploads/'.$fileName;
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
        $this->validate($request, [
            'id'      => 'required'
        ]);
        return Category::where('id', $request->id)->delete();
        // $category = Category::findOrFail('id', $request->id);
        // if($category->iconImage){
        //     @unlink(public_path('/uploads/'.$category->iconImage));
        //     $category->delete();
        // }
        // return;
    }
}
