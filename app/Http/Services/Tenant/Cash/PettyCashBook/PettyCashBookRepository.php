<?php

namespace App\Http\Services\Tenant\Cash\PettyCashBook;

use App\Models\Tenant\Cash\PettyCash;
use App\Models\Tenant\Cash\PettyCashBook;
use App\Models\Tenant\Cash\PettyCashServer;
use App\Models\Tenant\Maintenance\Company\ModuleChild;
use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Supply\Programming\Programming;
use Illuminate\Support\Facades\DB;

class PettyCashBookRepository
{
    public function insertPettyCashBook(array $dto): PettyCashBook
    {
        return PettyCashBook::create($dto);
    }

    public function udpatePettyCashBook(array $dto, int $id): PettyCashBook
    {
        $item = PettyCashBook::findOrFail($id);
        $item->update($dto);
        return $item;
    }

    public function updateCash(array $dto, int $id): PettyCash
    {
        $cash    =   PettyCash::findOrFail($id);
        $cash->update($dto);
        return $cash;
    }

    public function findCash(int $id): PettyCash
    {
        return PettyCash::findOrFail($id);
    }

    public function destroy(int $id): PettyCash
    {
        $cash    =   PettyCash::findOrFail($id);
        $cash->status   =   'ANULADO';
        $cash->save();
        return $cash;
    }

    public function getPettyCashBookInfo(int $id)
    {
        $cash_book  =   DB::table('petty_cash_books as pcb')
            ->join('users as u', 'u.id', 'pcb.user_id')
            ->select(
                'pcb.id',
                'pcb.petty_cash_name',
                'u.name as user_name',
                'pcb.initial_amount',
                'initial_date'
            )
            ->where('pcb.id', $id)
            ->first();
        return $cash_book;
    }

    public function searchCashAvailable($data)
    {
        $search = $data['search'] ?? null;

        $query  =   PettyCash::from('petty_cashes as pc')
            ->leftJoin('petty_cash_books as pcb', 'pc.id', 'pcb.petty_cash_id')
            ->where('pcb.status', 'CERRADO')
            ->orWhereNull('pcb.id')
            ->where('pc.status', 'CERRADO')
            ->distinct()
            ->when($search, function ($q) use ($search) {
                $q->where('pc.name', 'like', "%{$search}%");
            })
            ->select(
                'pc.id',
                'pc.name',
                'pc.status'
            )
            ->get();

        return $query;
    }

    public function pettyCashIsOpen(int $petty_cash_id)
    {
        $exists =   PettyCashBook::where('petty_cash_id', $petty_cash_id)->where('status', 'ABIERTO')->exists();
        return $exists;
    }

    public function getPettyCashBook(int $id)
    {
        return PettyCashBook::findOrFail($id);
    }

    public function getCashBookUser(int $user_id)
    {
        $cash_book  =   DB::table('petty_cash_books as pcb')
            ->join('petty_cashes as pc', 'pc.id', 'pcb.petty_cash_id')
            ->select(
                'pc.name',
                'pcb.id as petty_cash_book_id',
                'pc.id as petty_cash_id',
                'pc.name as petty_cash_name'
            )->where('pcb.status', 'ABIERTO')
            ->whereNull('pcb.final_date')
            ->where('pcb.user_id', $user_id)
            ->orderBy('pcb.id', 'ASC')
            ->first();

        return $cash_book;
    }

    public function getCashBookWaiter(int $user_id)
    {
        $cash_book  =   DB::table('petty_cash_servers as pcs')
            ->join('petty_cash_books as pcb', 'pcb.id', 'pcs.petty_cash_book_id')
            ->join('petty_cashes as pc', 'pc.id', 'pcb.petty_cash_id')
            ->select(
                'pcb.id as petty_cash_book_id',
                'pc.id as petty_cash_id',
                'pc.name as petty_cash_name'
            )
            ->where('pcs.user_id', $user_id)
            ->where('pcb.status', 'ABIERTO')
            ->whereNull('pcb.final_date')
            ->orderBy('pcb.id', 'ASC')
            ->first();

        return $cash_book;
    }

    public function getCashBookCash(int $cash_id)
    {
        $cash_book  =   DB::table('petty_cash_books as pcb')
            ->join('petty_cashes as pc', 'pc.id', 'pcb.petty_cash_id')
            ->select(
                'pc.name',
                'pcb.id as petty_cash_book_id',
                'pc.id as petty_cash_id',
                'pc.name as petty_cash_name'
            )->where('pcb.status', 'ABIERTO')
            ->whereNull('pcb.final_date')
            ->where('pcb.petty_cash_id', $cash_id)
            ->orderBy('pcb.id', 'ASC')
            ->first();

        return $cash_book;
    }

    public function serverIsAssigned(int $user_id, int $exception_id = null)
    {
        $item =   PettyCashServer::where('user_id', $user_id);

        if ($exception_id) {
            $item->where('petty_cash_book_id', '<>', $exception_id);
        }

        return $item->exists();
    }

    public function waiterInCash(int $user_id)
    {
        $item =   PettyCashServer::where('user_id', $user_id)->get();

        if (count($item) > 1) {
            return null;
        }

        if (count($item) === 0) {
            return false;
        }

        return $item->first();
    }

    public function insertPettyCashServers(array $dto)
    {
        PettyCashServer::insert($dto);
    }

    public function deletePettyCashServers(int $id)
    {
        PettyCashServer::where('petty_cash_book_id', $id)->delete();
    }

    public function getPettyCashServers(int $id)
    {
        return PettyCashServer::where('petty_cash_book_id', $id)->get();
    }

    public function getDeletedPettyCashServers(int $id, array $lst_servers)
    {
        return PettyCashServer::where('petty_cash_book_id', $id)
            ->whereNotIn('user_id', $lst_servers)
            ->get();
    }

    public function getOne(int $id): array
    {
        $petty_cash_book    = PettyCashBook::findOrFail($id);
        $servers            = PettyCashServer::where('petty_cash_book_id', $id)->get();
        return ['petty_cash_book' => $petty_cash_book, 'servers' => $servers];
    }

    public function hasProgrammingActive(int $petty_cash_book_id)
    {
        $programming    =   Programming::where('petty_cash_book_id', $petty_cash_book_id)->where('status', 'ACTIVO')->get();

        if (count($programming) > 1) {
            return false;
        }
        if (count($programming) === 0) {
            return null;
        }

        return $programming->first();
    }

    public function hasOrdersPending(int $id)
    {
        $has    =   Order::where('petty_cash_book_id', $id)->where('status', 'ACTIVO')->where('status_invoice', 'NO FACTURADO')->exists();
        return $has;
    }

    public function getProductsCanceled(int $id)
    {
        $products   =   DB::table('orders_products as op')
            ->join('orders as o', 'o.id', 'op.order_id')
            ->join('sales as s', 's.id', 'o.sale_id')
            ->where('s.petty_cash_book_id', $id)
            ->whereNull('s.converted_from_id')
            ->where('op.status', 'ANULADO')
            ->select(
                'o.code',
                'o.creator_user_name',
                'op.cancellation_date',
                DB::raw("'PRODUCTO' as item_type"),
                'op.product_name as item_name',
                'op.quantity as item_quantity',
                'op.sale_price as item_sale_price',
                'op.total AS  item_total'
            );

        $dishes = DB::table('orders_dishes as od')
            ->join('orders as o', 'o.id', 'od.order_id')
            ->join('sales as s', 's.id', 'o.sale_id')
            ->where('s.petty_cash_book_id', $id)
            ->whereNull('s.converted_from_id')
            ->where('od.status', 'ANULADO')
            ->select(
                'o.code',
                'o.creator_user_name',
                'od.cancellation_date',
                DB::raw("'PLATO' as item_type"),
                'od.dish_name as item_name',
                'od.quantity AS item_quantity',
                'od.sale_price AS item_sale_price',
                'od.total AS item_total'
            );

        $items_canceled =   $products->unionAll($dishes)->orderBy('cancellation_date', 'desc')->get();
        return $items_canceled;
    }


}
