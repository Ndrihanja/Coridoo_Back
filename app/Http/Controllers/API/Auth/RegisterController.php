<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController; 
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required',
            'prenom' => 'sometimes|string',
            'matricule' => 'required',
            'bio' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $role_id = str_starts_with($request->matricule, 'PE') ? '1' : '2';
            
   
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'matricule' => $request->matricule,
            'bio' => $request->bio,
            'email' => $request->email,
            'password' => $request->password,
            'active' => true,
            'role_id' => $role_id,
        ]);
        $token =  $user->createToken('coridoo_token')->accessToken;
        $data = [
            "user" => $user,
            "token" => $token
        ];
        return $this->sendResponse($data, 'Inscription avec succès.');
    }
}
