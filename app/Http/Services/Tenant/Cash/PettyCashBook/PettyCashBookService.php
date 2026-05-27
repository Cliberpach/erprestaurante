<?php

namespace App\Http\Services\Tenant\Cash\PettyCashBook;

use App\Http\Services\Tenant\Cash\PettyCash\CashService;
use App\Http\Services\Tenant\Consumables\WarehouseConsumable\WarehouseConsumableService;
use App\Http\Services\Tenant\Supply\Programming\ProgrammingService;
use App\Models\Company;
use App\Models\Tenant\Accounts\CustomerAccountDetail;
use App\Models\Tenant\Cash\PettyCashBook;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Supply\Programming\Programming;
use App\Models\Tenant\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\DB;

class PettyCashBookService
{
    private PettyCashBookRepository $s_repository;
    private PettyCashBookDto $s_dto;
    private PettyCashBookValidation $s_validation;
    private CashService $s_cash;
    private ProgrammingService $s_programming;
    private PettyCashBookCalculator $s_calculator;

    public function __construct()
    {
        $this->s_repository =   new PettyCashBookRepository();
        $this->s_dto        =   new PettyCashBookDto();
        $this->s_validation =   new PettyCashBookValidation($this->s_repository);
        $this->s_cash       =   new CashService();
        $this->s_calculator =   new PettyCashBookCalculator($this->s_repository);
    }

    public function openPettyCash(array $data): PettyCashBook
    {
        $data               =   $this->s_validation->validateOpenCash($data);

        $dto                =   $this->s_dto->getDtoStore($data);
        $petty_cash_book    =   $this->s_repository->insertPettyCashBook($dto);

        $dto_servers   =   $this->s_dto->getDtoCashServers($data['lst_servers'], $petty_cash_book->id);
        $this->s_repository->insertPettyCashServers($dto_servers);

        $this->s_cash->setStatus($dto['petty_cash_id'], 'ABIERTO');

        if (array_key_exists('programming_auto', $data)) {
            $this->s_programming =   new ProgrammingService();
            $this->s_programming->auto($petty_cash_book);
        }
        return $petty_cash_book;
    }

    public function getPdfOne(array $data)
    {
        $id =   $data['id'];

        //====== OBTENER MOVIMIENTO =======
        $petty_cash_book    =   $this->s_repository->getPettyCashBook($id);
        $cajero             =   User::findOrFail($petty_cash_book->user_id);
        $payment_methods    =   PaymentMethod::where('estado', 'ACTIVO')->get();

        $consolidated       =   $this->s_calculator->getConsolidated($id);
        $consolidated_items =   $this->getConsolidatedItems($id);

        //======= OBTENER DATOS DE LA EMPRESA ========
        $company = Company::first();

        $customer_pays      =   CustomerAccountDetail::from('customer_accounts_details as cad')
            ->join('customer_accounts as ca', 'ca.id', 'cad.customer_account_id')
            ->leftJoin('work_orders as wo', 'wo.id', '=', 'ca.work_order_id')
            ->leftJoin('sales as sd', 'sd.id', '=', 'ca.sale_id')
            ->where('cad.petty_cash_book_id', $id)
            ->select(
                'ca.document_serie',
                DB::raw("
                    CASE
                        WHEN ca.work_order_id IS NOT NULL THEN wo.customer_name
                        WHEN ca.sale_id IS NOT NULL THEN sd.customer_name
                        ELSE NULL
                    END AS customer_name
                "),
                'cad.cash',
                'cad.amount',
                'cad.total',
                'cad.payment_method_id',
                'cad.created_at'
            )->get();

        $have_module_customer_accounts      =   $consolidated['have_module_customer_accounts'];
        $consolidated_cash                  =   $this->s_calculator->consolidatedCash($petty_cash_book, $consolidated);

        //====== VISTA PDF ==========
        $pdf = Pdf::loadView(
            'cash.petty-cash-book.reports.pdf-one',
            [
                'petty_cash_book'   =>  $petty_cash_book,
                'company'           =>  $company,
                'payment_methods'   =>  $payment_methods,
                'cajero'            =>  $cajero,
                'consolidated'      =>  $consolidated,
                'customer_pays'     =>  $customer_pays,
                'consolidated_items' =>  $consolidated_items,
                'have_module_customer_accounts' =>  $have_module_customer_accounts,
                'consolidated_cash'             =>  $consolidated_cash,
            ]
        );

        //========= PAGINACIÓN 1/n =========
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $font   = $dompdf->getFontMetrics()->get_font("helvetica", "bold");
        $dompdf->get_canvas()->page_text(530, 800, "{PAGE_NUM} / {PAGE_COUNT}", $font, 10, array(0, 0, 0));

        //======= VISUALIZAR PDF ==========
        return $pdf->stream('caja_movimiento' . $petty_cash_book->id . '.pdf');
    }

    public function getCashBookUser(int $user_id)
    {
        return $this->s_repository->getCashBookUser($user_id);
    }

    public function getCashBookCash(int $cash_id)
    {
        return $this->s_repository->getCashBookCash($cash_id);
    }

    public function getCashBookWaiter(int $user_id)
    {
        return $this->s_repository->getCashBookWaiter($user_id);
    }

    public function closePettyCash(array $data)
    {
        $this->s_validation->validationClosePettyCash($data);
        $consolidated   =   $this->getConsolidated($data['id']);

        $petty_cash_book                    =   $this->s_repository->getPettyCashBook($data['id']);
        $petty_cash_book->status            =   'CERRADO';
        $petty_cash_book->closing_amount    =   $consolidated['amount_close'];
        $petty_cash_book->final_date        =   now();
        $petty_cash_book->save();

        $this->s_cash->setStatus($petty_cash_book->petty_cash_id, 'CERRADO');
        $this->s_repository->deletePettyCashServers($data['id']);

        $programming            =   $this->s_repository->hasProgrammingActive($data['id']);
        if ($programming === false) {
            throw new Exception("EL MOVIMIENTO DE CAJA: CM-" . $data['id'] . "TIENE MÁS DE UNA PROGRAMACIÓN ACTIVA");
        }
        if ($programming) {
            $this->s_programming    =   new ProgrammingService();
            $this->s_programming->setStatus($programming->id, 'CERRADO');
        }

        $this->substractConsumables($consolidated['consolidated_consumables']);
        return $petty_cash_book;
    }

    public function substractConsumables($data)
    {
        $data = $data->filter(function ($item) {
            return (float) $item->stock >= (float) $item->total;
        });

        $lst    =   $this->s_dto->formatConsolidateConsumables($data);
        $s_wc   =   new WarehouseConsumableService();
        $s_wc->decreaseLstStock($lst);
    }

    public function pettyCashIsOpen(int $petty_cash_id)
    {
        return $this->s_repository->pettyCashIsOpen($petty_cash_id);
    }

    public function getOne(int $id): array
    {
        return $this->s_repository->getOne($id);
    }

    public function update(array $data, int $id): PettyCashBook
    {
        $data               =   $this->s_validation->validateUpdateCash($data, $id);
        $dto                =   $this->s_dto->getDtoUpdate($data, $id);
        $petty_cash_book    =   $this->s_repository->udpatePettyCashBook($dto, $id);

        $dto_servers        =   $this->s_dto->getDtoCashServers($data['lst_servers'], $petty_cash_book->id);
        $this->s_repository->deletePettyCashServers($id);
        $this->s_repository->insertPettyCashServers($dto_servers);

        return $petty_cash_book;
    }

    public function hasProgrammingActive(int $petty_cash_book_id)
    {
        return $this->s_repository->hasProgrammingActive($petty_cash_book_id);
    }

    public function waiterInCash(int $user_id)
    {
        return $this->s_repository->waiterInCash($user_id);
    }

    public function programming(array $data): Programming
    {
        $cash_book              =   $this->s_repository->getPettyCashBook($data['id']);

        $this->s_programming    =   new ProgrammingService();
        $programming            =   $this->s_programming->auto($cash_book);

        return $programming;
    }

    public function getConsolidatedItems(int $petty_cash_book_id)
    {
        $items_canceled  =   $this->s_repository->getProductsCanceled($petty_cash_book_id);
        return $items_canceled;
    }

    public function getConsolidated(int $id)
    {
        return $this->s_calculator->getConsolidated($id);
    }
}
