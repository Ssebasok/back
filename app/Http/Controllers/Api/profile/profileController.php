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
        
        // Calcular total de ingresos por usuario ID
        $totalIncome = $this->calculateTotalIncomeByUserId($userId);
        
        // Obtener detalles de los ingresos
        $incomes = $this->getUserIncome($userId);
        
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
                'debts' => $debts,
                'total_income' => $totalIncome,
                'income_count' => count($incomes),
                'incomes' => $incomes
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
                ->where('deb', '1')
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
                ->where('deb', '1')
                ->select('id', 'description', 'amount', 'due_date', 'status')
                ->orderBy('due_date', 'asc')
                ->get();

            return $debts;
            
        } catch (\Exception $e) {
            return [];
        }
    }


    private function calculateTotalIncomeByUserId($userId)
    {
        try {
            $totalIncome = DB::table('debts')
                ->where('user_id', $userId)
                ->where('deb', '0')
                ->sum('amount');
            return $totalIncome;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getUserIncome($userId)
    {
        try {
            $incomes = DB::table('debts')
                ->where('user_id', $userId)
                ->where('deb', '0')
                ->select('id', 'description', 'amount', 'due_date', 'status')
                ->orderBy('due_date', 'asc')
                ->get();
            return $incomes;
        } catch (\Exception $e) {
            return [];
        }
    }


}