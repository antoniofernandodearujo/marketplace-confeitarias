<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CepService
{
    /**
     * Get address information from CEP using ViaCEP API
     */
    public function getAddressFromCep(string $cep)
    {
        try {
            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['erro'])) {
                    return [
                        'cep' => $cep,
                        'street' => $data['logradouro'],
                        'neighborhood' => $data['bairro'],
                        'city' => $data['localidade'],
                        'state' => $data['uf']
                    ];
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}