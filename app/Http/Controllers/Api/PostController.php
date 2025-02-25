<?php

namespace App\Http\Controllers\Api;

//import Model "Post"
use App\Models\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//import Resource "PostResource"
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
//import Facade "Validator"
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
    * index
    *
    * @return void
    */
    public function index()
    {
        //get all posts
        $posts = Post::latest()->paginate(5);
        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    /**
    * store
    *
    * @param mixed $request
    * @return void
    */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image' =>'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //create post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        //return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', 
        
$post);
}

    //function for updating data, have 2 param(request and id)
    public function update(Request $request, $id) {
        //check text length
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:10',
            'content' => 'required|min:10',
        ]);
        //check validator

        if ($validator->fails()) {
            // give 422 response if response not JSON
            return Response()->json($validator->errors(), 422);
        }

        //get id
        $post = Post::find($id);
        //check image if not empty
        if ($request->hasFile('image')) {
            //request for an image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hasName());
            //delete old image
            Storage::delete('public/posts', basename($post->image));
            //updating image, title and content data
            $post->update([
            'image' => $image->hasName(),
            'title' => $request->title,
            'content' => $request->content,          
            ]);
        } else {
            //update post without image
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }
        //message if data has been updated
        return new PostResource(true, 'Data post berhasil diubah', $post);
    }
    /**
     * @param int $id
     * @return \Illuminate\http\Response
     * */

     //function delete
     public function destroy ($id)
     {
        //get id
        $post = Post::find($id);
        //if data not posted, send 404 response
        if (!$post) {
            return response()->json(['message' => 'Post tidak ditemukan'], 404);
        }
        //delete data
        $post->delete();
        //response for deleting data
        return response()->json(['message' => 'Data berhasil dihapus'], 200);
     }
}