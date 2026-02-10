<?php

namespace App\Http\Controllers\Usuario;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {   
        
    }

    public function login(){

        request()->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = request()->only('email', 'password');
        if (Auth::guard('usuario')->attempt($credentials)) {
            return [
                'message' => 'Bienvenido!',
                'status' => 'success'
            ];
        }

        return ['message' => 'Este usuario no existe', 'status' => 'error'];
    }

    public function logout(){

        Session::flush();
        Auth::guard('usuario')->logout();
  
        return redirect()->route('web.home');
    }

    
    public function guard(){
        return Auth::guard('usuario');
    }

    protected function authenticated($request, $user){
        
            return response()->json([
                'success' => true,
                'target'  => 'hola'
            ]);
        
        // return redirect()->route('client.home');
    }
}
