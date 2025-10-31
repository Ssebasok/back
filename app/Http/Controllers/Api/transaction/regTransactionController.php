<?php

namespace App\Http\Controllers\Api\transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TransactionRequest;
use App\Models\Debt;

class regTransactionController extends Controller
{
    //
    public function createTransaction(TransactionRequest $request){

        try{
            $user = auth('api')->user();
    if(!$user){
        return response()->json([
            'success' => 0,
            'message' => 'Usuario no autenticado o no encontrado'
        ], 401);
    }
    $validated = $request->validated();
    $transaction = Debt::create([
        'user_id' => $user->id,
        'amount' => $validated['amount'],
        'description'=> $validated['description'] ?? null,
        'status'=> $validated['status']?? 'pending',
        'deb'=> $validated['deb']?? 1, 
        'due_date'=> $validated['due_date']?? null,
        'type_id'=> $validated['type_id'] ?? null,
        'installments' => $validated['installments']?? null

    ]);
    return response()->json([
        'success' => 1,
        'message' => 'Transacción creada exitosamente',
        'data' => $transaction
    ], 201);
} catch (\Exception $e) {
    return response()->json([
        'success' => 0,
        'message' => 'Error al crear la transacción: ' . $e->getMessage()
    ], 500);
       }

   }
}
