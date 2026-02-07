<?php

namespace App\Http\Services\Tenant\CreditNote;

use App\Http\Controllers\Tenant\QRController;
use App\Http\Services\Tenant\Inventory\WarehouseProduct\WarehouseProductService;
use App\Http\Services\Tenant\Invoicing\InvoicingManager;
use App\Http\Services\Tenant\Supply\Programming\ProgrammingService;
use App\Models\Company;
use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use App\Models\Tenant\DocumentSerialization;
use App\Models\Tenant\Sales\CreditNote\CreditNote;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditNoteService
{
    private CreditNoteDto $s_dto;
    private CreditNoteRepository $s_repository;
    private CreditNoteValidation $s_validation;

    public function __construct()
    {
        $this->s_dto    =   new CreditNoteDto();
        $this->s_repository =   new CreditNoteRepository();
        $this->s_validation =   new CreditNoteValidation();
    }

    public function storeFromSale(array $data): CreditNote
    {
        $invoice_type           =   $this->getTypeCreditNote($data['sale']->type_sale_code);
        $this->isActive($invoice_type);
        $correlative            =   $this->getCorrelative($invoice_type->id);
        $data['serie']          =   $correlative;
        $data['invoice_type']   =   $invoice_type;
        $dto                    =   $this->s_dto->getDtoFromSale($data);
        $instance               =   $this->s_repository->store($dto);

        if (isset($data['sale_products'])) {
            $dto_products           =   $this->s_dto->getDtoDetail($data['sale_products'], $instance->id);
            $this->s_repository->storeProducts($dto_products);
            $s_wps                  =   new WarehouseProductService();
            dd($data['sale_products']);
            $s_wps->increaseLstStock($data['sale_products']);
        }
        if (isset($data['sale_dishes'])) {
            $dto_dishes     =   $this->s_dto->getDtoDetail($data['sale_dishes'], $instance->id);
            $this->s_repository->storeDishes($dto_dishes);
            $s_programming  =   new ProgrammingService();
            $s_programming->increaseLstStock($data['sale_dishes']);
        }
        return $instance;
    }

    public function getTypeCreditNote(int $type_sale_code)
    {
        $parameter  =   null;
        $symbol     =   null;
        if ($type_sale_code == '01') {
            $parameter  =   'FF';
            $symbol     =   '07';
        }
        if ($type_sale_code == '03') {
            $parameter  =   'BB';
            $symbol     =   '07';
        }

        $invoice_type   =   GeneralTableDetail::where('symbol', $symbol)->where('parameter', $parameter)->first();
        return $invoice_type;
    }

    public function isActive($invoice_type)
    {

        $is_active      =   DocumentSerialization::where('document_type_id', $invoice_type->id)
            ->where('company_id', 1)
            ->first();

        if (!$is_active) {
            throw new Exception($invoice_type->name . ", NO ESTÁ ACTIVO EN LA EMPRESA");
        }

        return $invoice_type;
    }

    /*
{#2181 // app\Http\Services\Tenant\Sale\Sale\SaleService.php:41
  +"correlative": "1"
  +"serie": "NV01"
}
*/
    public static function getCorrelative($type_invoice_id): array
    {
        $correlative        =   null;
        $serie              =   null;

        //======= CONTABILIZANDO SI HAY DOCUMENTOS DE VENTA EMITIDOS PARA EL TYPE SALE ======
        $cant               =   CreditNote::where('type_invoice_id', $type_invoice_id)->count();
        $serialization      =   DocumentSerialization::where('company_id', 1)->where('document_type_id', $type_invoice_id)->first();

        //==== SI LA CANT ES 0 =====
        if ($cant === 0) {
            //====== INICIAR DESDE EL STARTING NUMBER =======
            $correlative        =   $serialization->start_number;
            $serie              =   $serialization->serie;
        } else {
            //======= EN CASO YA EXISTAN DOCUMENTOS DE VENTA DEL TYPE SALE ======
            $correlative        =   $cant  +   1;
            $serie              =   $serialization->serie;
        }

        return ['correlative' => $correlative, 'serie' => $serie];
    }

    public function sendSunat(int $credit_note_id): CreditNote
    {
        $credit_note    =   $this->s_repository->find($credit_note_id);
        $this->s_validation->validationSend($credit_note);

        $invoice_type   =   (object)['id' => $credit_note->type_invoice_id, 'name' => $credit_note->type_invoice_name];
        $this->isActive($invoice_type);

        $lst_detail     =   $this->s_repository->getDetail($credit_note_id);

        $dto            =   $this->s_dto->getDtoInvoicing($credit_note, $lst_detail);

        $s_invoice      =   new InvoicingManager();
        $data           =   $s_invoice->sendCreditNote($dto);
        $credit_note    =   $this->s_repository->saveSunatData($data, $credit_note);

        return $credit_note;
    }

    public function pdfOne(int $id)
    {
        $company        =   Company::findOrFail(1);
        $credit_note    =   $this->s_repository->find($id);
        $details        =   $this->s_repository->getDetail($id);

        $data_qr                =   (object)[
            'ruc_emisor'        =>  $company->ruc,
            'tipo_comprobante'  =>  $credit_note->type_invoice_code,
            'serie'             =>  $credit_note->serie,
            'correlativo'       =>  $credit_note->correlative,
            'mto_total_igv'     =>  number_format($credit_note->igv_amount, 2, '.', ''),
            'total'             =>  number_format($credit_note->total, 2, '.', ''),
            'fecha_emision'     =>  \Carbon\Carbon::parse($credit_note->created_at)->format('Y-m-d'),
            'tipo_documento_adquiriente'    =>  $credit_note->customer_document_code,
            'nro_documento_adquieriente'    =>  $credit_note->customer_document_number
        ];

        $res_qr         =   QRController::generateQr(json_encode($data_qr));
        $res_qr         =   $res_qr->getData();

        if ($res_qr->success) {
            $credit_note->ruta_qr =   $res_qr->data->ruta_qr;
            $credit_note->saveQuietly();
        }

        $pdf = PDF::loadview('sales.credit_notes.reports.pdf-one', [
            'company'           =>  $company,
            'credit_note'       =>  $credit_note,
            'details'           =>  $details
        ]);

        $pdf->setPaper('A4');

        return $pdf->stream($credit_note->serie . '-' . $credit_note->correlative . '.pdf');
    }
}
