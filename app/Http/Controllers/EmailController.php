<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;


class EmailController extends Controller
{
    public function store(Request $request){

        $email = new Email;

        $email->email = $request->email;

        $email->save();

        return redirect()->route('web.home');
    }

    public function index(){

        $emails = Email::orderBy('created_at')->get();

        return view('emails.index', compact('emails'));
    }


    public function destroy(Email $email){
        $email->delete();
        return redirect()->route('emails.index');
    }
}
