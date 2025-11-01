<?php

namespace App\Http\Controllers\Api\transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\delTransactionRequest;
use App\Models\Debt;




class delTransactionController extends Controller
{
    
    public function deleteTransaction(delTransactionRequest $request){

        try{
            $user = auth('api')->user();

            if(!$user){
                return response()->json([
                    'success' => 0,
                    'message' => 'Unauthenticated user'
                ], 401);
            }

            $transactionId = $request->input('transaction_id');
            $transaction = Debt::where('id', $transactionId)
            -> where('user_id', $user->id)
            ->first();

            if(!$transaction){
                return response()->json([
                    'success'=> 0,
                    'messaje'=> 'The Transaction is not find or does not belong to the user'
                ], 404);
            }
            $transaction->delete();
           return response()->json([

            'success'=> 1,
            'message'=> 'The transaction is deleted correctly',
            'data'=> [
                'id' => $transactionId
            ]
            ],200);

        }catch(\Exception $e){
            return response()-> json([
                'success'=> 0,
                'message'=> 'The transaction could not be deleted: '. $e->getMessage()

            ],500);
        }


    }
}
