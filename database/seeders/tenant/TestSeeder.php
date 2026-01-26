<?php

namespace Database\Seeders\tenant;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\Supply\TypeDish\TypeDish;

class TestSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | TIPOS DE PLATO
        |--------------------------------------------------------------------------
        */
        $types = [
            'ENTRADAS',
            'SEGUNDOS',
            'POSTRES',
        ];

        $typeDishMap = [];

        foreach ($types as $type) {
            $typeDishMap[$type] = TypeDish::create([
                'name' => $type,
            ])->id;
        }

        /*
        |--------------------------------------------------------------------------
        | LISTA DE PLATOS PERUANOS
        |--------------------------------------------------------------------------
        */
        $platos = [
            'ENTRADAS' => [
                'Causa Limeña',
                'Papa a la Huancaína',
                'Ocopa Arequipeña',
                'Tamales',
                'Anticuchos',
                'Leche de Tigre',
                'Choclo con Queso',
                'Papa Rellena',
                'Tequeños',
                'Ceviche de Pescado (Porción chica)',
            ],
            'SEGUNDOS' => [
                'Lomo Saltado',
                'Arroz con Pollo',
                'Ají de Gallina',
                'Seco de Carne',
                'Seco de Cabrito',
                'Tallarin Saltado',
                'Pollo a la Brasa',
                'Chaufa de Pollo',
                'Chaufa de Carne',
                'Carapulcra',
                'Estofado de Pollo',
                'Frejoles con Seco',
                'Pescado Frito',
                'Sudado de Pescado',
                'Cau Cau',
                'Tacu Tacu con Bistec',
                'Tacu Tacu con Pollo',
                'Chicharrón de Cerdo',
                'Mondonguito a la Italiana',
                'Arroz con Mariscos',
            ],
            'POSTRES' => [
                'Suspiro a la Limeña',
                'Arroz con Leche',
                'Mazamorra Morada',
                'Picarones',
                'Turrón de Doña Pepa',
                'Alfajores',
                'Chancaca con Queso',
                'Leche Asada',
                'Crema Volteada',
                'Kiwicha Dulce',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | PLATOS (50 POR CADA TIPO)
        |--------------------------------------------------------------------------
        */
        foreach ($platos as $type => $listaPlatos) {
            $typeDishId = $typeDishMap[$type];

            foreach ($listaPlatos as $plato) {
                Dish::create([
                    'name'            => $plato,
                    'type_dish_id'    => $typeDishId,
                    'sale_price'      => rand(12, 45),
                    'purchase_price' => rand(6, 25),
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MESAS (100)
        |--------------------------------------------------------------------------
        */
        for ($i = 1; $i <= 100; $i++) {
            Table::create([
                'name' => (string) $i,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | MARCAS
        |--------------------------------------------------------------------------
        */
        $brands = [
            'GLORIA',
            'COCA COLA',
            'PEPSICO',
            'BACKUS',
            'NESTLE',
            'AJE',
            'FIELD',
            'SAN JORGE',
            'DONOFRO',
            'COSTEÑO',
        ];

        $brandIds = [];
        foreach ($brands as $brand) {
            $brandIds[] = Brand::create([
                'name' => $brand,
            ])->id;
        }

        /*
        |--------------------------------------------------------------------------
        | CATEGORIAS
        |--------------------------------------------------------------------------
        */
        $categories = [
            'SNACKS',
            'GASEOSAS',
            'AGUAS',
            'HELADOS',
            'DULCES',
            'LACTEOS',
            'GALLETAS',
            'CERVEZAS',
            'JUGOS',
            'ETC',
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[] = Category::create([
                'name' => $category,
            ])->id;
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS BASE (REALES)
        |--------------------------------------------------------------------------
        */
        $productNames = [
            'PAPAS LAYS',
            'CHIZITOS',
            'DORITOS',
            'CHEETOS',
            'CHOCOLATE SUBLIME',
            'CHOCOLATE PRINCESA',
            'CHOCOLATE TRIANGULO',
            'GALLETAS OREO',
            'GALLETAS RITZ',
            'GALLETAS CHIPS AHOY',
            'GASEOSA COLA 500ML',
            'GASEOSA COLA 1L',
            'GASEOSA NARANJA',
            'AGUA SIN GAS 625ML',
            'AGUA CON GAS 500ML',
            'JUGO DURAZNO',
            'JUGO MANZANA',
            'LECHE ENTERA',
            'LECHE EVAPORADA',
            'YOGURT FRESA',
            'HELADO CHOCOLATE',
            'HELADO VAINILLA',
            'HELADO LUCUMA',
            'CERVEZA RUBIA',
            'CERVEZA NEGRA',
            'CHOCOLATE PARA TAZA',
            'CARAMELOS SURTIDOS',
            'CHUPETES',
            'GOMITAS',
            'BIZCOCHUELOS',
            'GALLETAS DE SODA',
            'MAIZ CANCHA',
            'MANI SALADO',
            'ARROZ EXTRA',
            'AZUCAR RUBIA',
            'AVENA',
            'FIDEOS',
            'SAL',
            'ACEITE VEGETAL',
            'MANTEQUILLA',
        ];

        /*
        |--------------------------------------------------------------------------
        | CREAR 200 PRODUCTOS
        |--------------------------------------------------------------------------
        */
        $productsPerCategory = 10;
        $productNamesCollection = collect($productNames);

        foreach ($categoryIds as $categoryIndex => $categoryId) {

            // tomar 10 nombres distintos para esta categoría
            $namesForCategory = $productNamesCollection
                ->slice($categoryIndex * $productsPerCategory, $productsPerCategory);

            foreach ($namesForCategory as $nameIndex => $name) {

                Product::create([
                    'category_id' => $categoryId,
                    'brand_id' => $brandIds[($categoryIndex + $nameIndex) % count($brandIds)],
                    'name' => $name,
                    'description' => 'PRODUCTO DE CONSUMO MASIVO',
                    'sale_price' => rand(2, 15),
                    'purchase_price' => rand(1, 10),
                    'stock' => 0,
                    'stock_min' => 1,
                    'code_factory' => null,
                    'code_bar' => null,
                    'img_route' => null,
                    'img_name' => null,
                    'unit_id' => 124,
                    'unit_name' => 'UNIDAD',
                    'unit_symbol' => 'NIU',
                ]);
            }
        }
    }
}
