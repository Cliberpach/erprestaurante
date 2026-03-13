<?php

namespace App\Http\Services\Tenant\Consumables\Consumable;

use App\Models\Product;
use App\Models\Tenant\Consumables\Consumable\Consumable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ConsumableRepository
{
    public function __construct() {}

    public function getProduct(int $product_id)
    {
        $product    =   DB::select('SELECT
                        p.id,
                        p.name,
                        br.name AS brand_name,
                        c.name AS category_name,
                        br.id AS brand_id,
                        c.id AS category_id
                        FROM products as p
                        INNER JOIN brands AS br ON br.id = p.brand_id
                        INNER JOIN categories AS c ON c.id = p.category_id
                        WHERE p.id = ?', [$product_id]);

        return $product;
    }

    public function store(array $data): Consumable
    {
        $instance    =   Consumable::create($data);
        return $instance;
    }

    public function update(int $id, array $data): Consumable
    {
        $instance    =   Consumable::findOrFail($id);
        $instance->update($data);
        return $instance;
    }

    public function getList(array $filters)
    {
        $categoria_id   =   $filters['categoria_id'];
        $marca_id       =   $filters['marca_id'];

        $items = DB::table('consumables as p')
            ->leftJoin('warehouse_consumables as wp', function ($join) {
                $join->on('wp.consumable_id', '=', 'p.id')
                    ->where('wp.warehouse_id', '=', 1); // Filtrar por almacen_id = 1
            })
            ->join('consumable_brands as b', 'b.id', '=', 'p.brand_id')
            ->join('consumable_categories as c', 'c.id', '=', 'p.category_id')
            ->select(
                'p.id',
                'p.brand_id',
                'p.category_id',
                'p.name',
                'p.sale_price',
                'p.purchase_price',
                DB::raw('IFNULL(wp.stock, 0) as stock'),
                'p.stock_min',
                'b.name as brand_name',
                'c.name as category_name',
                'wp.warehouse_id'
            )->where('p.status', 'ACTIVO');

        if ($categoria_id) {
            $items  =   $items->where('p.category_id', $categoria_id);
        }

        if ($marca_id) {
            $items  =   $items->where('p.brand_id', $marca_id);
        }

        return $items;

        //return DataTables::of($products)->make(true);
    }
}
