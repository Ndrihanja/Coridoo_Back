<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository; 

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $token =  $user->createToken('coridoo_token')->accessToken;
            $data = [
                "user" => $user,
                "token" => $token
            ];
   
            return $this->sendResponse($data, 'Connexion avec succès.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function logout()
    {
        $token = Auth::user()->token();

        $tokenRepository = app(TokenRepository::class);
        $tokenRepository->revokeAccessToken($token->id);

        return $this->sendResponse('tafa', 'Deconnexion avec succès');
    }
}
