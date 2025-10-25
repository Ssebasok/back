<?php


namespace App\Customs\Services;
use App\Models\EmailVerificationToken;
use Illuminate\Support\Str;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Http\Requests\ResendEmailNotificationRequest;




class EmailVerificationService{

    

    // send verification link
    public function sendVerificationLink(object $user): void{
        Notification::send($user, new EmailVerificationNotification($this->generateVerificationLink($user->email)));
        
    }

        // resend verification link

        function resendLink($email){
          $user = User::where('email', $email)->first();
          if($user){
            $this->sendVerificationLink($user);
            return response()->json([
              'success' => 1,
              'message' => 'Verification Link Sent Successfully'
            ], 200);
          }else{
            return response()->json([
              'success' => 0,
              'message' => 'User Not Found'
            ], 401);
          }
        }


    //verify user email
    public function verifyEmail( string $email, string $token ){
      $user = User::where('email', $email)->first(); 
      if (!$user){
        return response()->json([
          'success' => 0,
          'message' => 'User Not Found'
        ], 401);
      }

      // Verificar si ya está verificado
      if($user->email_verified_at != null){
        return response()->json([
          'success' => 0,
          'message' => 'Email Already Verified'
        ], 401);
      }

      // Verificar token
      $tokenRecord = EmailVerificationToken::where('token', $token)->where('email', $email)->first();
      if(!$tokenRecord){
        return response()->json([
          'success' => 0,
          'message' => 'Invalid Token'
        ], 401);
      }

      if($tokenRecord->expired_at <= now()){
        return response()->json([
          'success' => 0,
          'message' => 'Token Expired'
        ], 401);
      }

      // Si llegamos aquí, todo está bien
      $user->email_verified_at = now();
      $user->save();
      
      $tokenRecord->delete();
      
      return response()->json([
        'success' => 1,
        'message' => 'Email Verified Successfully'
      ], 200);
    }






    // check if user has already verified
    function checkIsEmailVerified(object $user){
    if($user->email_verified_at != null){
      return response()->json([
        'success' => 0,
        'message' => 'Email Already Verified'
      ], 401);
    }else{
      return $user;
    }

    }







    // verify token
    public function verifyToken(string $email, string $token){
      $token = EmailVerificationToken::where('token', $token)->where('email', $email)->first();
      if($token){
      if($token->expired_at > now()){
       return $token;

      }else{
        return response()->json([
            'success' => 0,
            'message' => 'Token Expired'
        ], 401); }
      }else {
        return response()->json([
            'success' => 0,
            'message' => 'Invalid Token'
        ], 401);
      }

    }


    
  // generate token
  public function generateVerificationLink(string $email): string{
    $checkIfTokenExists = EmailVerificationToken::where('email', $email)->first();
    if($checkIfTokenExists) $checkIfTokenExists->delete();
    $token = Str::uuid();
    $url = config('app.url'). "?token=".$token. "&email=".$email;
    $saveToken = EmailVerificationToken::create([
        'email' => $email,
        'token' => $token,
        'expired_at' => now()->addMinutes(10),
    ]);
    if($saveToken){
    return $url;}
  }
}