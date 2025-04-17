<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class CepService
{
    private const VIACEP_URL = 'https://viacep.com.br/ws/';

    /**
     * Busca o endereço através do CEP usando a API ViaCEP
     *
     * @param string $cep
     * @return array|null
     * @throws RequestException
     */
    public function getAddressFromCep(string $cep): ?array
    {
        // Remove caracteres não numéricos do CEP
        $cep = preg_replace('/[^0-9]/', '', $cep);

        try {
            $response = Http::get(self::VIACEP_URL . $cep . '/json');

            if ($response->successful()) {
                $data = $response->json();

                // Verifica se o CEP existe e não retornou erro
                if (!isset($data['erro'])) {
                    return [
                        'cep' => $data['cep'],
                        'street' => $data['logradouro'],
                        'neighborhood' => $data['bairro'],
                        'city' => $data['localidade'],
                        'state' => $data['uf']
                    ];
                }
            }

            return null;

        } catch (RequestException $e) {
            throw $e;
        }
    }
}