<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {

    /**
     * Mostrar el formulario de registro
     */
    public function showRegisterForm() {
        return view('auth.register');        
    }

    public function register(Request $request) {
        Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|string|email|unique:users",
            "password" => "required|string|min:8|confirmed"
        ], [
            "name.required" => "El campo de Nombre es requerido.",
            "email.required" => "El campo de Correo es requerido",
            "email.email" => "El valor de Correo no es un correo válido",
            "email.unique" => "El valor de Correo ya se encuentra registrado",
            "password.required" => "El campo de contraseña es requerido",
            "password.min" => "La contraseña debe contener minimo 8 caracteres",
            "password.confirmed" => "Las contraseñas no coinciden"
        ])->validate();

        $user = User::create($request->all());

        return redirect()->route('login');
    }

    /**
     * Mostrar el formulario de inicio de sesión
     */
    public function showLogin() {
        return view('auth.login');
    }

    /**
     * Recibe la solicitud para iniciar sesión
     * 
     * @param Request
     * @return RedirectResponse
     * 
     * @throws ValidationException cuando el usuario y contraseñas no son correctas
     */
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ], [
            'email.required' => 'El campo de Correo es requerido.',
            'password.required' => 'El campo de Contraseña es requerido'
        ]);

        if (Auth::guard()->attempt($request->only('email','password'))) {
            $request->session()->regenerate();

            return redirect()->route('tasks.index');
        }

        throw ValidationException::withMessages([
            'email' => 'El usuario o la contraseña son incorrectas!'
        ]);
    }

    /**
     * Destruye una sesion
     * 
     * @param Request
     * @return RedirectResponse
     * 
     */
    public function logout(Request $request) {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

}
