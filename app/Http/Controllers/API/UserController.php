<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Validator;
use Auth;
use App\Models\UsersModel;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function login(Request $request)
    {
    	try {
	    	$validator = Validator::make($request->all(), [ 
	            'email' => 'required',
	            'password' => 'required',
	        ]);

			if ($validator->fails()) { 
	            return parent::failResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
	        }

	        $param_login = [
	        	'email' => $request->get('email'), 
	        	'password' => $request->get('password'),
	        ];

	        if (Auth::attempt($param_login)){ 
	        	$user = Auth::user();
	        	$response['user'] = $user;
	        	$response['token'] =  $user->createToken('MyApp')->accessToken; 
	            return parent::successResponse($response);
	        }else{ 
	            return parent::failResponse("Invalid username or password", Response::HTTP_BAD_REQUEST);
	        }

	    } catch (\Exception $e) {
	    	return parent::failResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
	    } catch (\Throwable $e) {
	    	return parent::failResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function userData(Request $request)
    {
    	try {
    		$user = Auth::user();
    		return parent::successResponse($user);
    	} catch (\Exception $e) {
    		return parent::failResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
    	} catch(\Throwable $e) {
    		return parent::failResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
    	}
    }

    public function logout()
    { 
        try {
        	if (Auth::check()) {
	           Auth::user()->authAcessToken()->delete();
	        }
	        return parent::successResponse("Logout successfull");
	        
        } catch (\Exception $e) {
        	return parent::failResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
        	return parent::failResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
