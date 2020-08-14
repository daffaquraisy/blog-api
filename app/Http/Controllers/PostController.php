<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = \App\Post::all();
        return response()->json(['status' => 'success', 'data' => $posts]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:5|max:30',
            'desc' => 'required|min:10|max:600',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        if ($request->hasFile('photo')) {
            $filename = Str::random(10) . $request->title . '.jpg';

            $file = $request->file('photo');

            $file->move(base_path('public/images'), $filename);
        }

        \App\Post::create([
            'title' => $request->title,
            'desc' => $request->desc,
            'photo' => $filename
        ]);

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        $post = \App\Post::findOrFail($id);

        return response()->json(['status' => 'success', 'data' => $post]);
    }

    public function update(Request $request, $id)
    {
        $post = \App\Post::findOrFail($id);

        $filename = $post->photo;

        if ($request->hasFile('photo')) {
            $filename = Str::random(10) . $post->title . '.jpg';

            $file = $request->file('photo');

            $file->move(base_path('public/images'), $filename);

            unlink(base_path('public/images/' . $post->photo));
        }

        $post->update([
            'title' => $request->title,
            'desc' => $request->desc,
            'photo' => $filename
        ]);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $post = \App\Post::findOrFail($id);

        unlink(base_path('public/images/' . $post->photo));
        $post->delete();

        return response()->json(['status' => 'success']);
    }
}
