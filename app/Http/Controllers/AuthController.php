<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function showLogin(){
	    return view('auth.login');
	}

	public function doLogout(){
		Auth::logout(); // logging out user
		return Redirect::to('login'); // redirection to login screen
	}

	public function doLogin(Request $request){
		// Creating Rules for Email and Password
		$rules = array(
			'email' => 'required|email|exists:users,email', // make sure the email is an actual email
			'password' => 'required',
            'remember_me' => 'boolean'
			// password has to be greater than 3 characters and can only be alphanumeric and
        );
        $msg = [
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'El formato del correo es incorrecto',
            'email.exists' => 'El correo ingresado no existe en la base de datos',
            'password.required' => 'La contraseña es obligatoria'
        ];
		// checking all field
        $input = $request->all();
		$validator = Validator::make($input, $rules, $msg);
		// if the validator fails, redirect back to the form
		if ($validator->fails()){
			return Redirect::to('login')
                            ->withErrors($validator) // send back all errors to the login form
			                ->withInput($request->except('password')); // send back the input (not the password) so that we can repopulate the form
		}else{
			// create our user data for the authentication
			$userdata = array(
				'email' => $request->input('email') ,
				'password' => $request->input('password')
			);
			// attempt to do the login
			if (Auth::guard('web')->attempt($userdata, $request->remember_me)){
                $request->session()->regenerate();

				// validation successful
                $user = Auth::user();
                Log::info("Usuario conectado: " . $user->id);
                User::where('id', $user->id)->update(['last_login' => Carbon::now()]);
                return Redirect::to('/dashboard');
				// do whatever you want on success
			}else{

                return Redirect::to('login')
                            ->withErrors(['password' => 'La contraseña ingresada no es correcta'])->withInput($request->except('password')); // send back all errors to the login form
			}
		}
    }
}
