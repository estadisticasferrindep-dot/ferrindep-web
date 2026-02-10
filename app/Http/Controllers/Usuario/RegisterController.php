<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Usuario;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Mail\RegistroMailable;
use App\Models\Configpedido;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */



    /**
     * Where to redirect users after registration.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {   dd('hola');
        
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        
    }
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuarios'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        Usuario::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),

            'nombre' => $request->nombre,
            'empresa' => $request->empresa,
            'telefono' => $request->telefono,
            'cuit' => $request->cuit,
            'direccion' => $request->direccion,
            // 'tipo_cliente' => 'publico'


        ]);

        $configpedidos=Configpedido::get();
        $configpedido=$configpedidos->first();

        $correo = new RegistroMailable($request->all(),$configpedido);

       
        
        if ($configpedido->email1) {
            Mail::to($configpedido->email1)->send($correo);
        }
        if ($configpedido->email2) {
            Mail::to($configpedido->email2)->send($correo);
        }
        if ($configpedido->email3) {
            Mail::to($configpedido->email3)->send($correo);
        }

        Mail::to($request->email)->send($correo);


        return ['message' => 'Se ha registrado con éxito'];
    }
    
}
