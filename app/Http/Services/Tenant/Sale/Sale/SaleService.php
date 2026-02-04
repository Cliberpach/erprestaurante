<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Http\Controllers\Tenant\NumberToLettersController;
use App\Http\Services\Tenant\CreditNote\CreditNoteService;
use App\Http\Services\Tenant\Invoicing\InvoicingManager;
use App\Http\Services\Tenant\Maintenance\Company\CompanyManager;
use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use App\Models\Tenant\DocumentSerialization;
use App\Models\Tenant\Sales\Sale\Sale;
use Exception;

class SaleService
{
    private SaleValidation $s_validations;
    private CalculationsService $s_calculations;
    private CorrelativeService  $s_correlative;
    private SaleRepository $s_repository;
    private SaleDto $s_dto;
    private CompanyManager $s_company;

    public function __construct()
    {
        $this->s_repository     =   new SaleRepository();
        $this->s_validations    =   new SaleValidation($this->s_repository);
        $this->s_calculations   =   new CalculationsService();
        $this->s_correlative    =   new CorrelativeService();
        $this->s_company        =   new CompanyManager();
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

        $lstPays                =       json_decode($data['lstPays']);
        $validated_pays         =       $this->s_validations->validationLstPays($lstPays, $amounts);

        //========= OBTENIENDO CORRELATIVO Y SERIE =========
        $data_correlative       =       $this->s_correlative->getCorrelative($validated_data->type_sale_code);

        //====== LEGENDA ========
        $legend                 =       NumberToLettersController::numberToLetters($amounts->total);

        //======= GUARDAR MAESTRO VENTA =======
        $sale   =   $this->saveSale($validated_data, $amounts, $legend, $validated_pays, $data_correlative);

        //========= REGISTRAR DETALLE TYPE PRODUCTOS =======
        if ($validated_data->type === 'PRODUCTOS') {
            //$this->s_detail->storeDetail($sale, $validated_data);
        }

        //========= REGISTRAR DETALLE TYPE RESERVAS =======
        /*if($validated_data->type === 'RESERVAS'){
            $this->s_detail->storeDetailReservations($sale,$validated_data);
        }*/

        //======= INICIAR FACTURACIÓN =======
        $this->s_company->startInvoicing(1, $validated_data->type_sale_code);

        return $sale;
    }

    public function saveSale(object $validated_data, object $amounts, $legend, array $validated_pays, object $data_correlative): Sale
    {
        $sale                           =   new Sale();

        //======= GUARDANDO CLIENTE =======
        $sale->customer_id              =   $validated_data->customer->id;
        $sale->customer_name            =   $validated_data->customer->name;
        $sale->customer_type_document   =   $validated_data->customer->type_document_abbreviation;
        $sale->customer_document_number =   $validated_data->customer->document_number;
        $sale->customer_document_code   =   $validated_data->customer->type_document_code;
        $sale->customer_phone           =   $validated_data->customer->phone;

        //======= GUARDANDO USUARIO REGISTRADOR =======
        $sale->user_recorder_id         =   $validated_data->user_recorder->id;
        $sale->user_recorder_name       =   $validated_data->user_recorder->name;

        //====== GUARDANDO DATOS DE LA CAJA Y MOVIMIENTO DEL USUARIO =====
        $sale->petty_cash_id            =   $validated_data->petty_cash->petty_cash_id;
        $sale->petty_cash_name          =   $validated_data->petty_cash->petty_cash_name;
        $sale->petty_cash_book_id       =   $validated_data->petty_cash->petty_cash_book_id;

        //======== TIPO DE VENTA ======
        $sale->type_sale_code           =   $validated_data->type_sale_code;
        $sale->type_sale_name           =   $validated_data->type_sale_name;

        //====== MONTOS ======
        $sale->igv_percentage           =   $validated_data->igv_percentage;
        $sale->subtotal                 =   $amounts->subtotal;
        $sale->igv_amount               =   $amounts->igv_amount;
        $sale->total                    =   $amounts->total;
        $sale->legend                   =   $legend;

        //======= PAGOS =====
        $sale->method_pay_id_1          =   $validated_pays[0]->method_pay;
        $sale->amount_pay_1             =   $validated_pays[0]->amount;

        $sale->method_pay_id_2          =   $validated_pays[1]->method_pay;
        $sale->amount_pay_2             =   $validated_pays[1]->amount;

        //======== CORRELATIVO Y SERIE =======
        $sale->correlative              =   $data_correlative->correlative;
        $sale->serie                    =   $data_correlative->serie;
        $sale->save();

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

        $dto                    =   $this->s_dto->getDtoStoreFromOrder($data);

        $sale                   =   $this->s_repository->store($dto);

        $dto_sdishes            =   $this->s_dto->getDtoSaleDish($data['order_dishes'], $sale);
        $dto_s_products         =   $this->s_dto->getDtoProducts($data['order_products'], $sale);

        $this->s_repository->storeSaleDish($dto_sdishes);
        $this->s_repository->storeSaleProduct($dto_s_products);

        $dto_pays       =   $this->s_dto->getDtoPays($data['lst_pays'], $sale);
        $this->s_repository->storeSalePay($dto_pays);

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

    public function annular(array $data)
    {   
        $sale           =   $this->s_repository->findSale($data['sale_id']);
        $data['sale']   =   $sale;
        $this->s_validations->validationAnnular($sale);
        $s_credit_note  =   new CreditNoteService();
        $s_credit_note->storeFromSale($data);
    }
}
