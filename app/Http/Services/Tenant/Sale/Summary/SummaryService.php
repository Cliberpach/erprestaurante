<?php

namespace App\Http\Services\Tenant\Sale\Summary;

use App\Http\Services\Tenant\Invoicing\InvoicingManager;
use App\Http\Services\Tenant\Maintenance\Company\CompanyService;
use App\Models\Tenant\Maintenance\Company\Company;
use App\Models\Tenant\Maintenance\Company\DocumentSerialization;
use App\Models\Tenant\Sales\Summary\Summary;
use Exception;

class SummaryService
{
    private SummaryDto $s_dto;
    private SummaryRepository $s_repository;
    private CompanyService $s_company;
    private SummaryValidation $s_validation;

    public function __construct()
    {
        $this->s_dto    =   new SummaryDto();
        $this->s_repository =   new SummaryRepository();
        $this->s_company    =   new CompanyService();
        $this->s_validation =   new SummaryValidation();
    }

    public function store(array $data): Summary
    {
        $this->isActive();
        $data['data_correlative']  =   $this->getCorrelative();


        $lst_invoices       =   json_decode($data['comprobantes']);
        $fecha_comprobantes =   $data['fecha_comprobantes'];

        $dto_store  =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->store($dto_store);

        $dto_detail =   $this->s_dto->getDtoDetail($lst_invoices, $instance->id);
        $this->s_repository->storeDetail($dto_detail);

        $this->s_company->startInvoicingSymbol(1, 'R');
        $this->s_repository->setSalesSummary($instance);

        return $instance;
    }

    public function isActive()
    {
        $isActive   =   DocumentSerialization::where('symbol', 'R')->first();

        if (!$isActive) {
            throw new Exception('RESÚMENES NO ESTÁ ACTIVO EN LA EMPRESA!!!');
        }
    }

    /*
{#1844 // app\Http\Controllers\Ventas\RegistroVentaController.php:174
  +"correlativo": 1
  +"serie": "R001"
}
*/
    public function getCorrelative()
    {
        $correlative = null;
        $serie = null;

        $document_serialization = DocumentSerialization::where('symbol', 'R')->first();

        $last = Summary::orderBy('correlative', 'desc')->first();

        if (!$last) {
            $correlative = $document_serialization->start_number;
        } else {
            $correlative = $last->correlative + 1;
        }

        $serie = $document_serialization->serie;

        return (object)[
            'correlative' => $correlative,
            'serie' => $serie
        ];
    }

    public function sendSunat(int $id)
    {
        $instance    =   $this->s_repository->find($id);
        $this->s_validation->validationSend($instance);

        $this->isActive();

        $lst_detail     =   $this->s_repository->getDetail($id);

        $dto            =   $this->s_dto->getDtoInvoicing($instance, $lst_detail);

        $s_invoice      =   new InvoicingManager();
        $res_sunat      =   $s_invoice->sendSummary($dto);
        $instance       =   $this->s_repository->saveSunatData($res_sunat, $instance);

        $this->s_repository->updateStatusSales($id, $res_sunat['sunat_status']);

        return $res_sunat['message'];
    }

    public function consult($id)
    {
        $instance       =   $this->s_repository->find($id);
        $company        =   Company::findOrFail(1);

        $s_invoice      =   new InvoicingManager();
        $res_sunat      =   $s_invoice->consultSummary($instance->summary_result_ticket, $company->files_route, $instance->summary_name);
        $instance       =   $this->s_repository->saveSunatData($res_sunat, $instance);
        return $instance;
    }
}
