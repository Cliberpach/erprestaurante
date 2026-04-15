<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Http\Controllers\Tenant\NumberToLettersController;
use App\Http\Controllers\Tenant\QRController;
use App\Http\Services\Tenant\CreditNote\CreditNoteService;
use App\Http\Services\Tenant\Inventory\Kardex\KardexService;
use App\Http\Services\Tenant\Inventory\WarehouseProduct\WarehouseProductService;
use App\Http\Services\Tenant\Invoicing\InvoicingManager;
use App\Http\Services\Tenant\Maintenance\Company\CompanyService;
use App\Models\Landlord\Customer;
use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use App\Models\Tenant\DocumentSerialization;
use App\Models\Tenant\Maintenance\Company\Company;
use App\Models\Tenant\Sales\CreditNote\CreditNote;
use App\Models\Tenant\Sales\Sale\Sale;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleService
{
    private SaleValidation $s_validations;
    private CalculationsService $s_calculations;
    private CorrelativeService  $s_correlative;
    private SaleRepository $s_repository;
    private SaleDto $s_dto;
    private CompanyService $s_company;

    public function __construct()
    {
        $this->s_repository     =   new SaleRepository();
        $this->s_validations    =   new SaleValidation($this->s_repository);
        $this->s_calculations   =   new CalculationsService();
        $this->s_correlative    =   new CorrelativeService();
        $this->s_company        =   new CompanyService();
        $this->s_dto            =   new SaleDto();
    }

    public function store(array $data): Sale
    {
        //======== DOC VENTA ACTIVO ========
        $this->isActiveTypeSale($data['type_sale']);

        //======= VALIDACIÓN COMPLEJA =======
        $validated_data         =       $this->s_validations->validationStore($data);

        //======= OBTENIENDO MONTOS GLOBALES =======
        $amounts                =       $this->s_calculations->calculateAmounts($validated_data->lstSale, $validated_data->igv_percentage);
        $this->s_validations->validationAmounts($amounts, $validated_data);

        $lstPays                =       json_decode($data['lstPays']);
        $validated_pays         =       $this->s_validations->validationLstPays($lstPays, $amounts);
        $lstPays                =       $this->s_dto->formatPays($lstPays);

        //========= OBTENIENDO CORRELATIVO Y SERIE =========
        $validated_data->data_correlative   =   $this->s_correlative->getCorrelative($validated_data->type_sale_id);

        //====== LEGENDA ========
        $validated_data->legend             =   NumberToLettersController::numberToLetters($amounts->total);

        //======= GUARDAR MAESTRO VENTA =======
        $dto            =   $this->s_dto->getDtoStore($validated_data, $amounts);
        $sale           =   $this->s_repository->insertSale($dto);

        $lst_products           =   $this->s_dto->formatDetailSale($validated_data->lstSale);
        $dto_s_products         =   $this->s_dto->getDtoProducts($lst_products, $sale);
        $this->s_repository->storeSaleProduct($dto_s_products);
        $s_wp   =   new WarehouseProductService();
        $s_wp->decreaseLstStock($lst_products);

        if ($sale->payment_condition_id == 1) {
            $dto_pays       =   $this->s_dto->getDtoPays($lstPays, $sale);
            $this->s_repository->storeSalePay($dto_pays);
        }

        //======= INICIAR FACTURACIÓN =======
        $this->s_company->startInvoicing(1, $sale->type_sale_id);

        $s_kardex   =   new KardexService();
        $s_kardex->storeFromSale($sale);

        return $sale;
    }

    public static function isActiveTypeSale($type_sale)
    {
        $invoice_type   =   GeneralTableDetail::findOrFail($type_sale);

        $is_active  =   DocumentSerialization::where('document_type_id', $type_sale)
            ->where('company_id', 1)
            ->first();

        if (!$is_active) {
            throw new Exception($invoice_type->name . ", NO ESTÁ ACTIVO EN LA EMPRESA");
        }
    }

    public function calculateAmounts($data)
    {
        $lst_products   =   $data['lst_products'];
        $lst_services   =   $data['lst_services'];

        $subtotal   =   0;
        $igv_amount =   0;
        $total      =   0;

        foreach ($lst_products as $item) {
            $total  +=  (float)$item->quantity * (float)$item->sale_price;
        }
        foreach ($lst_services as $item) {
            $total  +=  (float)$item->quantity * (float)$item->sale_price;
        }

        $subtotal       =   $total / ((100 + (float)$data['igv_percentage']) / 100);
        $igv_amount     =   $total - $subtotal;

        $data['subtotal']       =   $subtotal;
        $data['igv_amount']     =   $igv_amount;
        $data['total']          =   $total;

        return $data;
    }

    public function storeFromCOrder(array $data): Sale
    {
        $this->isActiveTypeSale($data['invoice_id']);
        $data                   =   $this->s_validations->vStoreFromCOrder($data);

        $correlative            =   $this->s_correlative->getCorrelative($data['invoice_id']);
        $data['correlative']    =   $correlative;
        $data['amounts']        =   $this->s_calculations->calculateCAmounts($data);

        $dto                    =   $this->s_dto->getDtoStoreFromOrder($data);
        $sale                   =   $this->s_repository->store($dto);

        $dto_sdishes            =   $this->s_dto->getDtoSaleDish($data['order_dishes'], $sale);
        $dto_s_products         =   $this->s_dto->getDtoProducts($data['order_products'], $sale);

        $this->s_repository->storeSaleDish($dto_sdishes);
        $this->s_repository->storeSaleProduct($dto_s_products);

        $dto_pays       =   $this->s_dto->getDtoPays($data['lst_pays'], $sale);
        $this->s_repository->storeSalePay($dto_pays);

        $this->s_company->startInvoicing(1, $sale->type_sale_id);
        
        return $sale;
    }

    public function convert(array $data): Sale
    {
        $data                   =   $this->s_validations->validationConvert($data);
        $this->isActiveTypeSale($data['invoice']->id);

        $correlative            =   $this->s_correlative->getCorrelative($data['invoice']->id);
        $data['correlative']    =   $correlative;

        $dto                    =   $this->s_dto->getDtoConvert($data);
        $sale                   =   $this->s_repository->store($dto);

        $lst_products           =   $data['sale_products']->toArray();
        $lst_dishes             =   $data['sale_dishes']->toArray();

        $dto_products           =   $this->s_dto->getDtoDetailConvert($lst_products, $sale->id);
        $dto_dishes             =   $this->s_dto->getDtoDetailConvert($lst_dishes, $sale->id);

        $this->s_repository->storeSaleDish($dto_dishes);
        $this->s_repository->storeSaleProduct($dto_products);

        $this->s_repository->setConverted($data['sale'], $sale);
        return $sale;
    }

    public function sendSunat(int $sale_id): Sale
    {
        $sale           =   $this->s_repository->findSale($sale_id);
        $this->s_validations->validationSend($sale);
        $this->isActiveTypeSale($sale->type_sale_id);

        $lst_detail     =   $this->s_repository->getDetail($sale_id);
        $dto            =   $this->s_dto->getDtoInvoicing($sale, $lst_detail);

        $s_invoice      =   new InvoicingManager();
        $data           =   $s_invoice->sendInvoice($dto);
        $sale           =   $this->s_repository->saveSunatData($data, $sale);
        return $sale;
    }

    public function annular(array $data): CreditNote
    {
        $sale           =   $this->s_repository->findSale($data['sale_id']);
        $data['sale']   =   $sale;
        $sale_products  =   $this->s_repository->getSaleProducts($data['sale_id']);
        $sale_dishes    =   $this->s_repository->getSaleDishes($data['sale_id']);

        if ($sale_products->isNotEmpty()) {
            $data['sale_products']  =   $sale_products;
        }
        if ($sale_dishes->isNotEmpty()) {
            $data['sale_dishes']    =   $sale_dishes;
        }

        $this->s_validations->validationAnnular($sale);
        $s_credit_note  =   new CreditNoteService();
        $credit_note    =   $s_credit_note->storeFromSale($data);

        $this->s_repository->setSunatStatus($sale, 'ANULADO');
        return $credit_note;
    }

    public function getDetails(int $id)
    {
        return $this->s_repository->getDetail($id);
    }

    public function pdf_voucher(int $sale_id, $size): array
    {
        $company                =   Company::findOrFail(1);
        $sale                   =   Sale::findOrFail($sale_id);
        $sale_products          =   $this->s_repository->getSaleProducts($sale_id);
        $sale_dishes            =   $this->s_repository->getSaleDishes($sale_id);

        $route_view             =   'sales.sale_document.pdf.pdf';
        $pdf_size               =   null;

        $data_qr                =   (object)[
            'ruc_emisor'        =>  $company->ruc,
            'tipo_comprobante'  =>  $sale->type_sale_code,
            'serie'             =>  $sale->serie,
            'correlativo'       =>  $sale->correlative,
            'mto_total_igv'     =>  number_format($sale->igv_amount, 2, '.', ''),
            'total'             =>  number_format($sale->total, 2, '.', ''),
            'fecha_emision'     =>  \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d'),
            'tipo_documento_adquiriente'    =>  $sale->customer_document_code,
            'nro_documento_adquieriente'    =>  $sale->customer_document_number
        ];

        $res_qr         =   QRController::generateQr(json_encode($data_qr));
        $res_qr         =   $res_qr->getData();

        if ($res_qr->success && !$sale->ruta_qr) {
            $sale->ruta_qr =   $res_qr->data->ruta_qr;
            $sale->save();
        }

        $customer       =   Customer::findOrFail($sale->customer_id);

        if ((int)$size === 0) {
            $pdf_size   =   [0, 0, 226.772, 651.95];
        } else {
            $pdf_size   =   'A4';
            $route_view =   'sales.sale_document.pdf.pdf-a4';
        }

        $pdf = Pdf::loadview($route_view, [
            'company'               =>  $company,
            'sale'                  =>  $sale,
            'customer'              =>  $customer,
            'sale_products'         =>  $sale_products,
            'sale_dishes'           =>  $sale_dishes
        ]);

        $pdf->setPaper($pdf_size);

        $res    =   ['pdf' => $pdf, 'pdf_name' => $sale->customer_document_number . '-' . $sale->serie . '-' . $sale->correlative];

        return $res;
    }
}
