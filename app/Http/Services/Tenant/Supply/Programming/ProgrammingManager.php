<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Models\Tenant\Supply\Programming\Programming;
use Illuminate\Contracts\View\View;

class ProgrammingManager
{
    private ProgrammingService  $s_service;

    public function __construct()
    {
        $this->s_service    =   new ProgrammingService();
    }

    public function store(array $data): Programming
    {
        return $this->s_service->store($data);
    }

    public function update(array $data, int $id): Programming
    {
        return $this->s_service->update($data, $id);
    }

    public function getOne(int $id): Programming
    {
        return $this->s_service->getOne($id);
    }

    public function destroy(int $id): Programming
    {
        return $this->s_service->destroy($id);
    }

    public function edit(int $id): View
    {
        return $this->s_service->edit($id);
    }
}
