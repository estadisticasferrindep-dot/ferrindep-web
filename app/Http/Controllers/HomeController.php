<?php

namespace App\Http\Controllers;

use App\Models\Home;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){

        $homes = Home::get();

        return view('homes.index', compact('homes'));
    }


    public function update(Request $request, Home $home){

        $home->update($request->all());


        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store('public/imagenes');
            $home->logo = $logo;
        }

        if ($request->hasFile('logo_footer')) {
            $logo_footer = $request->file('logo_footer')->store('public/imagenes');
            $home->logo_footer = $logo_footer;
        }

        if ($request->hasFile('seccion_foto1')) {
            $seccion_foto1 = $request->file('seccion_foto1')->store('public/imagenes');
            $home->seccion_foto1 = $seccion_foto1;
        }

        if ($request->hasFile('seccion_foto2')) {
            $seccion_foto2 = $request->file('seccion_foto2')->store('public/imagenes');
            $home->seccion_foto2 = $seccion_foto2;
        }

        if ($request->hasFile('seccion_foto3')) {
            $seccion_foto3 = $request->file('seccion_foto3')->store('public/imagenes');
            $home->seccion_foto3 = $seccion_foto3;
        }





        if ($request->hasFile('fogo_foto')) {
            $fogo_foto = $request->file('fogo_foto')->store('public/imagenes');
            $home->fogo_foto = $fogo_foto;
        }
        if ($request->hasFile('acc_foto')) {
            $acc_foto = $request->file('acc_foto')->store('public/imagenes');
            $home->acc_foto = $acc_foto;
        }
        if ($request->hasFile('coc_foto')) {
            $coc_foto = $request->file('coc_foto')->store('public/imagenes');
            $home->coc_foto = $coc_foto;
        }
        $home->save();

        return redirect()->route('homes.index')->with('info','Información del inicio actualizada con éxito');
    }
}