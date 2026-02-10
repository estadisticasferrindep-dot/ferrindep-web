<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(){

        $videos = Video::orderBy('orden')->get();

        return view('videos.index', compact('videos'));
    }

    public function create(){
        return view('videos.create');
    }

    public function store(Request $request){


        $video = Video::create($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/videos');
            $video->imagen = $imagen;
        }

        $video->save();

        return redirect()->route('videos.index')->with('info','Video agregado con éxito');
    }

    public function edit(Video $video){
        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video){

        $video->update($request->all());

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('public/videos');
            $video->imagen = $imagen;
        }

        if (!$request->show) {
            $video->show = 0;
        }

        $video->save();

        return redirect()->route('videos.index')->with('info','Video actualizado con éxito');
    }

    
    public function destroy(Video $video){
        $video->delete();
        return redirect()->route('videos.index');
    }
}