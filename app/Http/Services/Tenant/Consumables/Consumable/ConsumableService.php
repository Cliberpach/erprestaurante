<?php

namespace App\Http\Services\Tenant\Consumables\Consumable;

use App\Http\Services\Tenant\Consumables\ConsumableIncomeNote\ConsumableIncomeNoteService;
use App\Http\Services\Tenant\Consumables\WarehouseConsumable\WarehouseConsumableService;
use App\Models\Company;
use App\Models\Tenant\Consumables\Consumable\Consumable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ConsumableService
{
    private ConsumableIncomeNoteService $s_income_note;
    private WarehouseConsumableService $s_warehouse;
    private ConsumableRepository $s_repository;
    private ConsumableDto $s_dto;

    public function __construct()
    {
        $this->s_income_note        =   new ConsumableIncomeNoteService();
        $this->s_warehouse          =   new WarehouseConsumableService();
        $this->s_repository         =   new ConsumableRepository();
        $this->s_dto                =   new ConsumableDto();
    }

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
        $dto    =   $this->s_dto->getDtoStore($data);

        //======== REGISTRAR PRODUCTO =======
        $instance    =   $this->s_repository->store($dto);

        //======= CREAR NOTA INGRESO O REGISTRAR PRODUCTO CON STOCK 0 ======
        $this->s_warehouse->increaseStock(1, $instance->id, $instance->stock);

        if ($instance->stock != 0) {
            $this->s_income_note->storeFromConsumable($instance);
        }

        //====== GUARDAR IMG =======
        $this->saveImagePublic($data['image'] ?? null, $instance);

        return $instance;
    }

    public function update(int $id, array $data): Consumable
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->update($id, $dto);
        $this->saveImagePublic($data['image'] ?? null, $instance);
        return $instance;
    }

    public function saveImagePublic($file_img, $instance)
    {
        if ($file_img) {

            $files_route        =   Company::first()->files_route;
            $path_destiny       =   public_path("storage/{$files_route}/consumables");

            //======== VERIFICAR DESTINO ========
            if (!File::exists($path_destiny)) {
                File::makeDirectory($path_destiny, 0755, true);
            }

            //======= ELIMINAR IMG PREVIA ========
            $this->deleteImg($instance);

            $extension          =   $file_img->getClientOriginalExtension();
            $name_img           =   'consumable_' . uniqid() . '.' . $extension;
            $name_file          =   $name_img;

            $file_img->move($path_destiny, $name_file);

            $instance->img_route =  "storage/{$files_route}/consumables/" . $name_img;
            $instance->img_name  =   $name_img;
            $instance->saveQuietly();
        }
    }

    public function deleteImg(Consumable $instance)
    {
        if ($instance->img_route && file_exists(public_path($instance->img_route))) {
            unlink(public_path($instance->img_route));
            $instance->img_route =   null;
            $instance->img_name  =   null;
            $instance->saveQuietly();
        }
    }

    public function getList(array $filter)
    {
        return $this->s_repository->getList($filter);
    }
}
