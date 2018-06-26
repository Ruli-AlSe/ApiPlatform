<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;

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
    	$role = 'ROLE_USER';
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null;
        $image = (!is_null($json) && isset($params->image)) ? $params->image : null;

    	if (!is_null($email) && !is_null($name) && !is_null($password)) 
    	{
    		//Crear usuario
    		$user = new User();
    		$user->email = $email;
    		$user->name = $name;
    		$user->image = $image;
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


    public function loginFB(Request $request)
    {
        //Recoger variables por POST
        $json = $request->input('json',null);
        $params = json_decode($json);

        //return response()->json($json, 200);

        //Asignar valores
        $id_sm = (!is_null($json) && isset($params->id)) ? $params->id : null;
        $first_name = (!is_null($json) && isset($params->first_name)) ? $params->first_name : null;
        $last_name = (!is_null($json) && isset($params->last_name)) ? $params->last_name : null;
        $social_media = (!is_null($json) && isset($params->sm)) ? $params->sm : null;
        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;

        if (!is_null($email) && !is_null($id_sm)) 
        {
            //Crear usuario
            $user = new User();
            $user->id_sm = $id_sm;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->social_media = $social_media;
            $user->email = $email;

            //Comprobar usuario duplicado
            //$user_id = User::select('id')->where('email', $email)->first();
            $user_sm = User::select('social_media')->where('id_sm', $id_sm)->first();
            $user_email = User::select('email')->where('id_sm', $id_sm)->first();
            if (isset($user_email) && $user_email->email == $email && $user_sm->social_media == $social_media) 
            {
                //NO Guardar el usuario
                $data = array('status' => 'success', 'code' => '200', 'message' => 'Usuario identificado.' );
            }
            elseif (isset($user_email) && $user_email->email == $email && $user_sm->social_media != $social_media) {
                $user->save();
                $data = array('status' => 'success', 'code' => '200', 'message' => 'Usuario almacenado.', $user_sm);
            }
            elseif (!isset($user_email)) 
            {
                $user->save();
                $data = array('status' => 'success', 'code' => '200', 'message' => 'Usuario almacenado.');
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
    	$jwtAuth = new JwtAuth();

    	//Recibir Post
    	$json = $request->input('json', null);
    	$params = json_decode($json);

    	$email = (!is_null($json) && isset($params->email)) ? $params->email : null;
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null;
    	$getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;

    	//Cifrar contraseña
    	$pwd = hash('sha256', $password);

    	if (!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')) 
    	{
    		$signup = $jwtAuth->signup($email, $pwd);
    	}
    	elseif ($getToken != null) 
    	{
    		$signup = $jwtAuth->signup($email, $pwd, $getToken);
    	}
    	else
    	{
    		$signup = array('status' => 'error', 'message' => 'Envia los datos por POST');
    	}

    	return response()->json($signup, 200);
    }
}
