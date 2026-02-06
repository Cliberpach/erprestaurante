<?php

namespace App\Services;

use App\Http\Services\Tenant\Inventory\Kardex\KardexService;
use App\Models\Product;
use App\Models\Tenant\Cash\PettyCashBook;
use App\Models\Tenant\NoteIncome;
use App\Models\Tenant\NoteIncomeDetail;
use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\Programming\Programming;
use App\Models\Tenant\Supply\Programming\ProgrammingDetail;
use App\Models\Tenant\WarehouseProduct;
use Illuminate\Support\Facades\DB;

class TestService
{
    public function createTestData(): void
    {
        DB::connection('tenant')->transaction(function () {

            PettyCashBook::create([
                'petty_cash_id'   => 1,
                'petty_cash_name' => 'PRINCIPAL',

                'shift_id'        => 1,
                'user_id'         => 2,

                'initial_amount'  => 0,
                'closing_amount'  => 0,

                'initial_date'    => now(),
                'final_date'      => null,

                'sale_day'        => 1,
            ]);


            /*
        |--------------------------------------------------------------------------
        | NOTA DE INGRESO
        |--------------------------------------------------------------------------
        */
            $noteIncome = NoteIncome::create([
                'user_recorder_id' => 1,
                'user_recorder_name' => 'ADMIN',
                'observation' => 'INGRESO INICIAL DE PRODUCTOS',
            ]);

            /*
        |--------------------------------------------------------------------------
        | DETALLE DE NOTA DE INGRESO (TODOS LOS PRODUCTOS)
        |--------------------------------------------------------------------------
        */
            $products = Product::with(['brand', 'category'])->get();

            foreach ($products as $product) {
                NoteIncomeDetail::create([
                    'note_income_id' => $noteIncome->id,
                    'product_id' => $product->id,
                    'brand_id' => $product->brand_id,
                    'category_id' => $product->category_id,

                    'warehouse_id' => 1,
                    'warehouse_name' => 'CENTRAL',

                    'product_name' => $product->name,
                    'brand_name' => $product->brand->name ?? '',
                    'category_name' => $product->category->name ?? '',

                    'quantity' => 200,
                ]);

                WarehouseProduct::create([
                    'warehouse_id'  =>  1,
                    'product_id'    =>  $product->id,
                    'stock'         =>  200
                ]);
            }

            //========= KARDEX NOTA ==========
            $s_kardex   =   new KardexService();
            $s_kardex->storeFromNoteIncome($noteIncome);

            /*
        |--------------------------------------------------------------------------
        | OBTENER CAJA ABIERTA (o usa ID directo si ya lo tienes)
        |--------------------------------------------------------------------------
        */
            $pettyCashBook = PettyCashBook::where('petty_cash_id', 1)
                ->latest()
                ->first();

            /*
        |--------------------------------------------------------------------------
        | CREAR PROGRAMMING
        |--------------------------------------------------------------------------
        */
            $programming = Programming::create([
                'petty_cash_book_id' => $pettyCashBook->id,
                'petty_cash_id'      => 1,
                'petty_cash_name'    => 'PRINCIPAL',

                'user_id'            => auth()->id(),

                'quantity_dishes'    => Dish::count(),
                'total'              => 0,
                'status'             => 'ACTIVO',
            ]);

            /*
        |--------------------------------------------------------------------------
        | CREAR PROGRAMMING DETAIL (TODOS LOS PLATOS)
        |--------------------------------------------------------------------------
        */
            $total = 0;

            $dishes = Dish::with('typeDish')->get();

            foreach ($dishes as $dish) {

                $quantity = 200; // programación base (puedes cambiarlo)

                ProgrammingDetail::create([
                    'programming_id' => $programming->id,
                    'dish_id'        => $dish->id,

                    'dish_name'      => $dish->name,
                    'type_dish_name' => $dish->typeDish->name ?? '',

                    'quantity'       => $quantity,
                    'stock'          => $quantity,

                    'purchase_price' => $dish->purchase_price,
                    'sale_price'     => $dish->sale_price,

                    'status'         => 'ACTIVO',
                ]);

                $total += $dish->purchase_price * $quantity;
            }

            /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR TOTAL
        |--------------------------------------------------------------------------
        */
            $programming->update([
                'total' => $total,
            ]);
        });
    }
}
