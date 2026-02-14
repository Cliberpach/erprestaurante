<?php

namespace App\Http\Services\Tenant\Supply\Dish;

use App\Models\Company;
use App\Models\Tenant\Supply\Dish\Dish;
use Illuminate\Support\Facades\File;

class DishService
{
    private DishRepository $s_repository;
    private DishDto $s_dto;

    public function __construct()
    {
        $this->s_repository =   new DishRepository();
        $this->s_dto        =   new DishDto();
    }

    public function store(array $data): Dish
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $item   =   $this->s_repository->insert($dto);
        $this->saveImg($dto, $data['img'] ?? null);
        return $item;
    }

    public function saveImg($dto, $img)
    {
        if (isset($dto['img_route']) && isset($dto['img_name'])) {
            $carpet_company =   Company::findOrFail(1)->files_route;
            $path           =   public_path("storage/{$carpet_company}/dishes/images/");
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $img->move($path, $dto['img_name']);
        }
    }

    public function deleteImg($dish)
    {
        if ($dish->img_route) {

            $path = public_path($dish->img_route);

            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    public function update(array $data, int $id): Dish
    {
        $dto            =   $this->s_dto->getDtoUpdate($data, $id);

        $dish_preview   =   $this->s_repository->find($id);
        $item           =   $this->s_repository->update($dto, $id);

        $this->deleteImg($dish_preview);
        $this->saveImg($dto, $data['img'] ?? null);

        return $item;
    }

    public function getOne(int $id): Dish
    {
        return $this->s_repository->find($id);
    }

    public function destroy(int $id): Dish
    {
        return $this->s_repository->destroy($id);
    }

    public function setStatus(int $id, string $status)
    {
        $this->s_repository->setStatus($id, $status);
    }

    public function searchDish(array $data)
    {
        $query = trim($data['q'] ?? '');

        if (empty($query)) {
            return [];
        }

        $items = Dish::from('dishes as d')
            ->join('types_dish as td', 'td.id', 'd.type_dish_id')
            ->where('d.status', 'ACTIVO')
            ->where(function ($q) use ($query) {
                $q->where('d.name', 'LIKE', "%{$query}%")
                    ->orWhere('td.name', 'LIKE', "%{$query}%");
            })->limit(20)
            ->get(
                [
                    'd.id',
                    'd.name',
                    'td.name as type_name'
                ]
            );

        $data = $items->map(fn($s) => [
            'id' => $s->id,
            'text' => "{$s->name}",
            'subtext'    => $s->type_name
        ]);

        return $data;
    }
}
