<?php

namespace App\Http\Controllers\Api\Auth;

use App\Customs\Services\EmailVerificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest; 
use App\Http\Requests\RegistationRequest;
use App\Models\User;
use App\Http\Requests\VerifyEmailRequest;
use App\Http\Requests\resendEmailNotificationRequest;


class AuthController extends Controller
{
    public function __construct(private EmailVerificationService $service){}


    public function login(LoginRequest $request){
        $token = auth('api')->attempt($request -> validated());
        if($token){
            return $this->responseWithToken($token, auth('api')->user());
        }else {
            return response()->json([
                'success' => 0,
                'message' => 'Invalid Credentials'
            ], 401);
        }
    }



    // resend verification link
    public function resendEmailVerificationLink(resendEmailNotificationRequest $request){
        return $this->service->resendLink($request->email);
    }
   //verify email

   public function verifyUserEmail(VerifyEmailRequest $request ){

    return $this->service->verifyEmail($request->email, $request->token);
   
   }



    public function register(RegistationRequest $request){

        $user = User::create($request->validated());
        if($user){
            $this-> service-> sendVerificationLink($user);
            $token = auth('api')->login($user);
            return $this->responseWithToken($token, $user);
        } else{
            return response()->json([
                'success' => 0,
                'message' => 'An Error Occured Creating User'
            ], 500);
        }
    }


   public function responseWithToken($token, $user){
   return response() -> json([
   
    'success' => 1,
    'user' => $user ,
    'access_token' => $token,
    'type' => 'bearer',


   ]);
}
    
}
