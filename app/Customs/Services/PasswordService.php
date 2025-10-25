<?php   

namespace App\Customs\Services;
use Illuminate\Support\Facades\Hash;

class PasswordService{
 
    public function validateCurrentPassword($currentPassword){
        if (!password_verify($currentPassword, auth()->user()->password)){
            response()->json([
                'success' => 0,
                'message' => 'Current Password is incorrect'
            ], 401);
        }
    }


    public function changePassword($data){
        $this->validateCurrentPassword($data['current_password']);
        #Current Password
        $updatePassword= auth()->user()->update([
            'password' => Hash::make($data['password'])
        ]);
        if($updatePassword){
            return response()->json([
                'success' => 1,
                'message' => 'Password Changed Successfully'
            ], 200);
        }else{
            return response()->json([
                'success' => 0,
                'message' => 'Password Change Failed'
            ], 401);
        }
    }
}