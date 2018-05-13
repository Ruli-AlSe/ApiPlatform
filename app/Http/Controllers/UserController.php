<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
    	//Recoger variables por POST
    	$json = $request->input('json',null);
    	$params = json_decode($json);

    	//Asignar valores
    	$email = (!is_null($json) && isset($params->email)) ? $params->email : null;
    	$name = (!is_null($json) && isset($params->name)) ? $params->name : null;
    	$surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
    	$role = 'ROLE_USER';
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null;

    	if (!is_null($email) && !is_null($name) && !is_null($password)) 
    	{
    		//Crear usuario
    		$user = new User();
    		$user->email = $email;
    		$user->name = $name;
    		$user->surname = $surname;
    		$user->role = $role;

    		//Cifrar contraseña
    		$pwd = hash('sha256', $password);
    		$user->password = $pwd;

    		//Comprobar usuario duplicado
    		$isset_user = User::where('email', '=', $email)->first();
    		if (count($isset_user) == 0) 
    		{
    			//Guardar el usuario
    			$user->save();
    			$data = array('status' => 'success', 'code' => '200', 'message' => 'usuario creado exitosamente.' );
    		}
    		else
    		{
    			//no guarar usuario
    			$data = array('status' => 'error', 'code' => '400', 'message' => 'usuario no fue creado, el usuario ya existe.' );
    		}
    	}
    	else
    	{
    		$data = array('status' => 'error', 'code' => '400', 'message' => 'usuario no fue creado, datos insuficientes.' );
    	}

    	return response()->json($data, 200);
    }

    public function login(Request $request)
    {
    	echo "Controlador de usuario, metodo login";
    	die();
    }
}
