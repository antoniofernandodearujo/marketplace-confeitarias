<?php

namespace App\Http\Controllers;

use App\Models\Confectionery;
use App\Models\Address;
use App\Http\Requests\StoreConfectioneryRequest;
use App\Services\CepService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfectioneryController extends Controller
{
    protected $cepService;

    public function __construct(CepService $cepService)
    {
        $this->cepService = $cepService;
    }

    public function index()
    {
        $confectioneries = Confectionery::with(['address', 'products.images'])->get();
        
        return response()->json([
            'confectioneries' => $confectioneries,
            'products' => $confectioneries->pluck('products')->flatten()
        ]);
    }

    public function store(StoreConfectioneryRequest $request)
    {
        try {
            DB::beginTransaction();

            // If CEP is provided, fetch address details
            if ($request->input('address.cep')) {
                $cepData = $this->cepService->getAddressFromCep($request->input('address.cep'));
                
                if ($cepData) {
                    // Pre-fill address fields if not provided
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

            // Create address first
            $address = Address::create($request->input('address'));

            // Create confectionery with address
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
            return response()->json(['error' => 'Erro ao criar confeitaria'], 500);
        }
    }

    public function show(Confectionery $confectionery)
    {
        return response()->json($confectionery->load(['address', 'products.images']));
    }

    public function update(Request $request, Confectionery $confectionery)
    {
        try {
            DB::beginTransaction();

            // Update address if provided
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

            // Update confectionery fields if provided
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
            \Log::error('Erro ao atualizar confeitaria: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao atualizar confeitaria'], 500);
        }
    }

    public function destroy(Confectionery $confectionery)
    {
        try {
            DB::beginTransaction();
            
            // Due to onDelete('cascade') in migrations, this will automatically delete:
            // 1. All products associated with this confectionery
            // 2. All product images
            // 3. The address
            $confectionery->delete();
            
            DB::commit();
            return response()->json(null, 204);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao deletar confeitaria'], 500);
        }
    }
}
