<?php

namespace App\Http\Controllers;

use App\Models\Confectionery;
use App\Services\CepService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    private $cepService;

    public function __construct(CepService $cepService)
    {
        $this->cepService = $cepService;
    }

    public function fetchAddressByCep(Request $request): JsonResponse
    {
        $request->validate([
            'cep' => 'required|string|size:8'
        ]);

        try {
            $address = $this->cepService->getAddressFromCep($request->cep);

            if (!$address) {
                return response()->json([
                    'message' => 'CEP não encontrado'
                ], 404);
            }

            return response()->json($address);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar CEP'
            ], 500);
        }
    }
}
