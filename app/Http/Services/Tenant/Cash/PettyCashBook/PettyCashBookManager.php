<?php

namespace App\Http\Services\Tenant\Cash\PettyCashBook;

use App\Models\Tenant\Cash\PettyCashBook;

class PettyCashBookManager
{
    private PettyCashBookService $s_cashbook;

     public function __construct()
    {
        $this->s_cashbook    =   new PettyCashBookService();
    }

    public function openPettyCash(array $data):PettyCashBook{
        return $this->s_cashbook->openPettyCash($data);
    }

    public function getPdfOne(array $data){
        return $this->s_cashbook->getPdfOne($data);
    }

    public function getConsolidated(int $id){
        return $this->s_cashbook->getConsolidated($id);
    }

    public function closePettyCash(array $data){
        return $this->s_cashbook->closePettyCash($data);
    }

    public function getOne(int $id):array{
        return $this->s_cashbook->getOne($id);
    }

    public function update(array $data,int $id):PettyCashBook{
        return $this->s_cashbook->update($data,$id);
    }

}
