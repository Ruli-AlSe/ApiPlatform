<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;


class JwtAuth{

	public $key;

	public function __construct()
	{
		//$this->key = 'mi-llave-segura-1234567890';
		$this->key = 'Pl@t@forma-@n@lisi_Extr@ccion-d@atos_UG-2_0-1_8';
	}

	public function signup($email, $password, $getToken=null)
	{
		$user = User::where(array('email' => $email, 'password' => $password))->first();

		$signup = false;
		if(is_object($user)) 
		{
			$signup = true;
		}

		if($signup) 
		{
			# Generar el token y devolverlo
			$token = array('sub' => $user->id, 'name' => $user->name, 'surname' => $user->surname, 'iat' => time(), 'exp' => time() + (7 * 24 * 60 * 60));

			#codificar token y llave
			$jwt = JWT::encode($token, $this->key, 'HS256');
			$decoded = JWT::decode($jwt, $this->key, array('HS256'));

			if (is_null($getToken)) 
			{
				return $jwt;
			}
			else
			{
				return $decoded;
			}
		}
		else
		{
			# Devolver error
			return array('status' => 'error', 'message' => 'El login ha fallado');
		}
	}

	public function checkToken($jwt, $getIdentity = false)
	{
		$auth = false;

		try{
			$decoded = JWT::decode($jwt, $this->key, array('HS256'));
		}
		catch(\UnexpectedValueException $e)
		{
			$auth = false;
		}
		catch(\DomainException $e)
		{
			$auth = false;
		}

		if (isset($decoded) && is_object($decoded) && isset($decoded->sub)) 
		{
			$auth = true;
		}
		else
		{
			$auth = false;
		}


		if ($getIdentity) 
		{
			return $decoded;
		}

		return $auth;
	}
}