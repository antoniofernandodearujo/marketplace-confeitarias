<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        // Primeiro vamos garantir que a pasta de destino exista e preparar as imagens
        $this->prepareProductImages();
        
        // Inserindo endereços mockados
        $addresses = [
            [
                'cep' => '13083970',
                'street' => 'Av. Albert Einstein',
                'number' => '1251',
                'neighborhood' => 'Cidade Universitária',
                'city' => 'Campinas',
                'state' => 'SP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cep' => '01310200',
                'street' => 'Av. Paulista',
                'number' => '1374',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cep' => '22250040',
                'street' => 'Rua Voluntários da Pátria',
                'number' => '190',
                'neighborhood' => 'Botafogo',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $addressIds = [];
        foreach ($addresses as $address) {
            $addressIds[] = DB::table('addresses')->insertGetId($address);
        }

        // Inserindo confeitarias mockadas
        $confectioneries = [
            [
                'name' => 'Doces da Maria',
                'latitude' => -22.817207,
                'longitude' => -47.069650,
                'phone' => '(19) 99999-1111',
                'address_id' => $addressIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Confeitaria Paulista',
                'latitude' => -23.561534,
                'longitude' => -46.655124,
                'phone' => '(11) 99999-2222',
                'address_id' => $addressIds[1],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Doces Cariocas',
                'latitude' => -22.951916,
                'longitude' => -43.184590,
                'phone' => '(21) 99999-3333',
                'address_id' => $addressIds[2],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $confectioneryIds = [];
        foreach ($confectioneries as $confectionery) {
            $confectioneryIds[] = DB::table('confectioneries')->insertGetId($confectionery);
        }

        // Inserindo produtos mockados
        $products = [
            [
                'name' => 'Bolo de Chocolate',
                'price' => 89.90,
                'description' => 'Delicioso bolo de chocolate com cobertura de brigadeiro',
                'confectionery_id' => $confectioneryIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Torta de Morango',
                'price' => 79.90,
                'description' => 'Torta de morango com creme de baunilha',
                'confectionery_id' => $confectioneryIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brigadeiros Gourmet',
                'price' => 45.00,
                'description' => 'Caixa com 12 brigadeiros gourmet',
                'confectionery_id' => $confectioneryIds[1],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cheesecake',
                'price' => 95.00,
                'description' => 'Cheesecake de frutas vermelhas',
                'confectionery_id' => $confectioneryIds[1],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pudim de Leite',
                'price' => 35.00,
                'description' => 'Tradicional pudim de leite condensado',
                'confectionery_id' => $confectioneryIds[2],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quindim',
                'price' => 40.00,
                'description' => 'Bandeja com 20 quindins',
                'confectionery_id' => $confectioneryIds[2],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $productIds = [];
        foreach ($products as $product) {
            $productIds[] = DB::table('products')->insertGetId($product);
        }

        // Inserindo imagens mockadas para os produtos
        $images = [
            [
                'image_path' => 'products/bolo-chocolate-1.jpg',
                'product_id' => $productIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'products/bolo-chocolate-2.jpg',
                'product_id' => $productIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'products/torta-morango-1.jpg',
                'product_id' => $productIds[1],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'products/brigadeiros-1.jpg',
                'product_id' => $productIds[2],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'products/cheesecake-1.jpg',
                'product_id' => $productIds[3],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'image_path' => 'products/pudim-1.jpg',
                'product_id' => $productIds[4],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($images as $image) {
            DB::table('product_images')->insert($image);
        }
    }
    
    /**
     * Prepara e copia as imagens de produtos para o storage
     */
    private function prepareProductImages(): void
    {
        // Cria a pasta de destino se não existir
        $destinationPath = storage_path('app/public/products');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }
        
        // Lista de imagens para copiar
        $images = [
            'torta-morango-1.jpg',
            'brigadeiros-1.jpg',
            'cheesecake-1.jpg',
            'pudim-1.jpg',
        ];
        
        // Copiamos as imagens do diretório de sementes para o storage público
        foreach ($images as $image) {
            $sourcePath = database_path('seeders/images/' . $image);
            $targetPath = $destinationPath . '/' . $image;
            
            // Se a imagem fonte existir, copiamos para o destino
            if (File::exists($sourcePath)) {
                File::copy($sourcePath, $targetPath);
            } else {
                // Se não existir, criamos uma imagem placeholder
                $this->createPlaceholderImage($targetPath, $image);
            }
        }
        
        // Criamos o symlink para o storage se não existir
        $this->createStorageLink();
    }
    
    /**
     * Cria uma imagem placeholder se a imagem original não existir
     */
    private function createPlaceholderImage(string $path, string $filename): void
    {
        // Cria uma imagem básica com o nome do arquivo
        $img = imagecreatetruecolor(400, 300);
        $bgColor = imagecolorallocate($img, 230, 230, 230);
        $textColor = imagecolorallocate($img, 50, 50, 50);
        
        imagefill($img, 0, 0, $bgColor);
        imagestring($img, 5, 100, 140, 'Placeholder: ' . $filename, $textColor);
        
        imagejpeg($img, $path, 90);
        imagedestroy($img);
    }
    
    /**
     * Cria o symbolic link para o storage se não existir
     */
    private function createStorageLink(): void
    {
        $publicPath = public_path('storage');
        
        // Se o link simbólico não existir, tentamos criá-lo
        if (!File::exists($publicPath)) {
            try {
                // Criamos o link manualmente em vez de usar o comando Artisan
                File::link(storage_path('app/public'), $publicPath);
            } catch (\Exception $e) {
                // Em caso de erro, mostramos apenas uma mensagem
                echo "Não foi possível criar o link simbólico para o storage.\n";
                echo "Execute o comando: php artisan storage:link\n";
            }
        }
    }
}
