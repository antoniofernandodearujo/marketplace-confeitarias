<?php

namespace App\Http\Controllers;

use App\Models\Confectionery;
use App\Models\Address;
use App\Http\Requests\StoreConfectioneryRequest;
use App\Services\CepService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para gerenciamento de confeitarias
 * 
 * Esta classe permite criar, listar, atualizar e excluir confeitarias,
 * além de gerenciar seus respectivos endereços de forma automática.
 */
class ConfectioneryController extends Controller
{
    /**
     * Serviço de consulta de CEP
     * 
     * @var CepService
     */
    protected $cepService;

    /**
     * Construtor do controlador
     * 
     * @param CepService $cepService Serviço de consulta de CEP para autocompletar endereços
     */
    public function __construct(CepService $cepService)
    {
        $this->cepService = $cepService;
    }

    /**
     * Lista todas as confeitarias com seus endereços e produtos
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Carrega as confeitarias com seus relacionamentos para evitar consultas N+1
        $confectioneries = Confectionery::with(['address', 'products.images'])->get();
        
        return response()->json([
            'confectioneries' => $confectioneries,
            'products' => $confectioneries->pluck('products')->flatten()
        ]);
    }

    /**
     * Cria uma nova confeitaria com endereço
     * 
     * @param StoreConfectioneryRequest $request Requisição validada
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreConfectioneryRequest $request)
    {
        try {
            DB::beginTransaction();

            // Se o CEP foi fornecido, busca informações de endereço
            if ($request->input('address.cep')) {
                $cepData = $this->cepService->getAddressFromCep($request->input('address.cep'));
                
                if ($cepData) {
                    // Preenche campos de endereço automaticamente se não fornecidos
                    $request->merge([
                        'address' => array_merge($request->input('address', []), [
                            'street' => $request->input('address.street', $cepData['street']),
                            'neighborhood' => $request->input('address.neighborhood', $cepData['neighborhood']),
                            'city' => $request->input('address.city', $cepData['city']),
                            'state' => $request->input('address.state', $cepData['state'])
                        ])
                    ]);
                }
            }

            // Cria o endereço primeiro
            $address = Address::create($request->input('address'));

            // Cria a confeitaria associada ao endereço
            $confectionery = Confectionery::create([
                'name' => $request->input('name'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'phone' => $request->input('phone'),
                'address_id' => $address->id
            ]);

            DB::commit();
            return response()->json($confectionery->load('address'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar confeitaria: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao criar confeitaria'], 500);
        }
    }

    /**
     * Exibe detalhes de uma confeitaria específica
     * 
     * @param Confectionery $confectionery A confeitaria a ser exibida (injetada pelo Laravel)
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Confectionery $confectionery)
    {
        return response()->json($confectionery->load(['address', 'products.images']));
    }

    /**
     * Atualiza dados de uma confeitaria existente
     * 
     * @param Request $request Dados da requisição
     * @param Confectionery $confectionery A confeitaria a ser atualizada
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Confectionery $confectionery)
    {
        try {
            DB::beginTransaction();

            // Atualiza o endereço se fornecido
            if ($request->has('address')) {
                $addressData = collect($request->input('address'))->only([
                    'cep', 'street', 'number', 'neighborhood', 'city', 'state'
                ])->filter()->toArray();
                
                if (!empty($addressData)) {
                    $address = $confectionery->address;
                    if (!$address) {
                        $address = new Address($addressData);
                        $address->save();
                        $confectionery->address()->associate($address);
                    } else {
                        $address->update($addressData);
                    }
                }
            }

            // Atualiza os campos da confeitaria se fornecidos
            $confectioneryData = collect([
                'name' => $request->input('name'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'phone' => $request->input('phone')
            ])->filter()->toArray();

            if (!empty($confectioneryData)) {
                $confectionery->update($confectioneryData);
            }

            DB::commit();
            return response()->json($confectionery->fresh()->load(['address', 'products.images']));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar confeitaria: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao atualizar confeitaria'], 500);
        }
    }

    /**
     * Remove uma confeitaria e seus dados relacionados
     * 
     * @param Confectionery $confectionery A confeitaria a ser excluída
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Confectionery $confectionery)
    {
        try {
            DB::beginTransaction();
            
            // Devido ao onDelete('cascade') nas migrações, isto automaticamente excluirá:
            // 1. Todos os produtos associados a esta confeitaria
            // 2. Todas as imagens dos produtos
            // 3. O endereço da confeitaria
            $confectionery->delete();
            
            DB::commit();
            return response()->json(null, 204);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar confeitaria: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao deletar confeitaria'], 500);
        }
    }
}
