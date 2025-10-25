<?php

namespace App\Http\Controllers\Api\profile;

use Illuminate\Http\Request;
use App\Http\Requests\homeRequest;
use App\Http\Controllers\Controller;

class profileController extends Controller
{
    public function home(homeRequest $request)
    {
        $user = auth('api')->user();
        
        return response()->json([
            'success' => 1,
            'message' => 'Home endpoint funcionando correctamente',
            'user' => $user,
            'token_info' => [
                'user_id' => auth('api')->id(),
                'expires_at' => auth('api')->payload()->get('exp')
            ]
        ]);
    }
}