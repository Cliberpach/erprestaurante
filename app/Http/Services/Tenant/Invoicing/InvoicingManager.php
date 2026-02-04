<?php

namespace App\Http\Services\Tenant\Invoicing;

use App\Http\Services\Tenant\Invoicing\GuiaFacturacion\GuiaFacturacionService;
use App\Http\Services\Tenant\Invoicing\Invoice\InvoiceService;
use App\Models\Tenant\Maintenance\Company\CompanyInvoice;
use Exception;
use Greenter\Api;
use Greenter\See;
use Greenter\Utils\Util;
use Greenter\Ws\Services\SunatEndpoints;

class InvoicingManager
{

    protected GuiaFacturacionService $s_guia_fac;
    private InvoiceService $s_invoice;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //$this->s_guia_fac       =   new GuiaFacturacionService();
        $this->s_invoice        =   new InvoiceService();
    }

    public function consultarGuiaSunat() {}


    //===== INSTANCIAR UTILIDADES GREENTER ======
    public function getUtil(): Util
    {
        $util   =    Util::getInstance();
        return $util;
    }

    public function sendInvoice(array $dto)
    {
        $util   =   $this->getUtil();
        $see    =   $this->config($util);
        return $this->s_invoice->sendInvoice($dto, $util, $see);
    }


    /*
========= OBTENER CONFIGURACIÓN DEL AMBIENTE GREENTER =======
{#1764 // app\Http\Services\Facturacion\FacturacionManager.php:88
  +"certificado_ruta": "greenter/certificado/certificado.pem"
  +"usuario_api_guias": "test-85e5b0ae-255c-4891-a595-0b98c65c9854"
  +"clave_api_guias": "test-Hty/M6QshYvPgItX2P0+Kw=="
  +"ruc": "20609678047"
  +"razon_social": "CORPORACION CHAGUALITO S.A.C."
  +"direccion": "TU DIRECCION #123"
  +"ubigeo": "130101"
  +"usuario_sol": "FACT2022"
  +"clave_sol": "Fact2022"
  +"modo": "BETA"
}
*/
    public function config(Util $util): See
    {
        $see    =   null;
        $config             =   CompanyInvoice::from('company_invoices as ci')
            ->join('companies as c', 'c.id', 'ci.company_id')
            ->select(
                'ci.certificate_url',
                'ci.api_user_gre',
                'ci.api_password_gre',
                'c.ruc',
                'c.business_name',
                'c.fiscal_address',
                'c.zip_code',
                'ci.secondary_user',
                'ci.secondary_password',
                'ci.environment',
                'c.files_route'
            )->where('c.id', 1)->first();


        if (!$config) {
            throw new Exception("NO EXISTE LA CONFIGURACIÓN EN LA TABLA EMPRESAS");
        }
        if (!$config->environment) {
            throw new Exception("AMBIENTE DE FACTURACIÓN NO ENCONTRADO");
        }

        if (!$config->secondary_user) {
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_USER');
        }

        if (!$config->secondary_password) {
            throw new Exception('DEBE ESTABLECER LA CREDENCIAL SOL_PASS');
        }

        if (!$config->api_user_gre) {
            throw new Exception('DEBE ESTABLECER EL ID API GUÍA DE REMISIÓN');
        }

        if (!$config->api_password_gre) {
            throw new Exception('DEBE ESTABLECER LA CLAVE API GUÍA DE REMISIÓN');
        }

        if ($config->environment !== "BETA" && $config->environment !== "PRODUCCION") {
            throw new Exception('NO SE HA CONFIGURADO EL AMBIENTE BETA O PRODUCCIÓN PARA LA FACTURACIÓN');
        }

        if ($config->environment === "BETA") {
            //===== MODO BETA DEMO ======
            $see = $util->getSee(SunatEndpoints::FE_BETA, $config);
        }

        if ($config->environment === "PRODUCCION") {
            //===== MODO PRODUCCION ======
            $see = $util->getSee(SunatEndpoints::FE_PRODUCCION, $config);
        }

        if (!$see) {
            throw new Exception('ERROR EN LA CONFIGURACIÓN DE GREENTER, SEE ES NULO');
        }

        return $see;
    }
}
