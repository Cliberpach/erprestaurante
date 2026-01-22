<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Models\Tenant\Supply\Programming\Programming;

class ProgrammingManager
{
    private ProgrammingService  $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new ProgrammingService();
    }

    public function store(array $data): Programming
    {
        return $this->s_manager->store($data);
    }

    public function update(array $data, int $id): Programming
    {
        return $this->s_manager->update($data, $id);
    }

    public function getOne(int $id): Programming
    {
        return $this->s_manager->getOne($id);
    }

    public function destroy(int $id): Programming
    {
        return $this->s_manager->destroy($id);
    }

}
