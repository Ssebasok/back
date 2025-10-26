<?php

namespace App\Http\Controllers\Api\profile;

use Illuminate\Http\Request;
use App\Http\Requests\homeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class profileController extends Controller
{
    public function home(homeRequest $request)
    {
        $user = auth('api')->user();
        $userId = $user->id;
        
        // Calcular total de deudas por usuario ID
        $totalDebt = $this->calculateTotalDebtByUserId($userId);
        
        // Obtener detalles de las deudas
        $debts = $this->getUserDebts($userId);
        
        return response()->json([
            'success' => 1,
            'message' => 'Datos del perfil obtenidos exitosamente',
            'user' => $user,
            'token_info' => [
                'user_id' => $userId,
                'expires_at' => auth('api')->payload()->get('exp')
            ],
            'financial_data' => [
                'total_debt' => $totalDebt,
                'debt_count' => count($debts),
                'debts' => $debts
            ]
        ]);
    }

    /**
     * Calcular el total de deudas por ID de usuario
     */
    private function calculateTotalDebtByUserId($userId)
    {
        try {
            // Calcular el total de deudas pendientes del usuario
            $totalDebt = DB::table('debts')
                ->where('user_id', $userId)
                ->where('status', 'PENDING')
                ->sum('amount');
            
            return $totalDebt;
            
        } catch (\Exception $e) {
            // En caso de error, retornar 0
            return 0;
        }
    }

    /**
     * Obtener detalles de las deudas del usuario
     */
    private function getUserDebts($userId)
    {
        try {
            $debts = DB::table('debts')
                ->where('user_id', $userId)
                ->where('status', 'PENDING')
                ->select('id', 'description', 'amount', 'due_date', 'status')
                ->orderBy('due_date', 'asc')
                ->get();

            return $debts;
            
        } catch (\Exception $e) {
            return [];
        }
    }
}