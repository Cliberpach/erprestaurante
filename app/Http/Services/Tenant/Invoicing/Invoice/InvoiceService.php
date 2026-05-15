<?php

namespace App\Http\Services\Tenant\Invoicing\Invoice;

use App\Models\Tenant\Sales\Sale\Sale;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\See;
use Greenter\Utils\Util;

class InvoiceService
{
    public function getDtoSale(int $sale_id)
    {
        $sale   =   Sale::findOrFail($sale_id);
    }

    public function sendInvoice(array $dto, Util $util, See $see)
    {
        //======== INSTANCIAR OBJETO FACTURA O BOLETA ========
        $invoice = new Invoice();

        $dto_client =   $dto['client'];
        $client = new Client();
        $client->setTipoDoc(ltrim($dto_client['tipoDoc'], '0'))
            ->setNumDoc($dto_client['numDoc'])
            ->setRznSocial($dto_client['rznSocial'])
            ->setAddress((new Address())
                ->setDireccion($dto_client['address']))
            ->setEmail($dto_client['email'])
            ->setTelephone($dto_client['telephone']);

        //======= CONSTRUIR FACTURA ENCABEZADO ======
        $invoice
            ->setUblVersion('2.1')
            //->setFecVencimiento(new DateTime($dto['fecVencimiento']))
            ->setTipoOperacion($dto['tipoOperacion'])
            ->setTipoDoc(str_pad($dto['tipoDoc'], 2, '0', STR_PAD_LEFT))
            ->setSerie($dto['serie'])
            ->setCorrelativo($dto['correlativo'])
            ->setFechaEmision(new DateTime($dto['fechaEmision']))
            ->setFormaPago(new FormaPagoContado())
            ->setTipoMoneda('PEN')
            ->setCompany($util->shared->getCompany())
            ->setClient($client)
            ->setMtoOperGravadas($dto['mtoOperGravadas'])
            ->setMtoIGV(round($dto['mtoIGV'], 2))
            ->setTotalImpuestos($dto['totalImpuestos'])
            ->setValorVenta($dto['valorVenta'])
            ->setSubTotal((float)$dto['subTotal'])
            ->setMtoImpVenta((float)$dto['mtoImpVenta']);

        if ($dto['discount'] > 0) {
            $invoice->setDescuentos([
                (new Charge())
                    ->setCodTipo('02') // Catalog. 53
                    ->setMontoBase($dto['discount_base'])
                    ->setFactor(1)
                    ->setMonto($dto['discount_base'])
            ]);
        }

        //======== CONSTRUIR DETALLE BOLETA/FACTURA ========
        $items      =   [];
        $detail     =   $dto['details'];

        foreach ($detail as $product) {
            $items[] = (new SaleDetail())
                ->setCodProducto($product['codProducto'])
                ->setUnidad($product['unidad'])
                ->setDescripcion($product['descripcion'])
                ->setCantidad($product['cantidad'])
                ->setMtoValorUnitario($product['mtoValorUnitario'])
                ->setMtoValorVenta($product['mtoValorVenta'])
                ->setMtoBaseIgv($product['mtoBaseIgv'])
                ->setPorcentajeIgv($product['porcentajeIgv'])
                ->setIgv($product['igv'])
                ->setTipAfeIgv($product['tipAfeIgv'])
                ->setTotalImpuestos($product['totalImpuestos'])
                ->setMtoPrecioUnitario($product['mtoPrecioUnitario']);
        }

        $invoice->setDetails($items)
            /*->setLegends([
                (new Legend())
                    ->setCode('1000')
                    ->setValue($dto['legends'])
            ])*/;

            dd($invoice);

        $res = $see->send($invoice);
        dd($res);
        $data    =   [
            'response_success'          =>  null,
            'response_error'            =>  null,
            'response_error_code'       =>  null,
            'response_error_message'    =>  null,
            'cdr'                       =>  null,
            'response_cdrZip'           =>  null,
            'sunat_status'              =>  'PENDIENTE',
            'cdr_response_id'           =>  null,
            'cdr_response_code'         =>  null,
            'cdr_response_description'  =>  null,
            'cdr_response_notes'        =>  null,
            'cdr_response_reference'    =>  null,
            'route_cdr'                 =>  null,
            'route_xml'                 =>  null,
            'message'                   =>  null
        ];

        $util->writeXml($invoice, $see->getFactory()->getLastXml(), $dto['tipoDoc'], $dto['company']->files_route, null);

        if ($dto['tipoDoc']   ==  1) {
            $data['route_xml']  =   'storage/' . $dto['company']->files_route . '/greenter/facturas/xml/' . $invoice->getName() . '.xml';
        }

        if ($dto['tipoDoc']   ==  3) {
            $data['route_xml']  =   'storage/' . $dto['company']->files_route . '/greenter/boletas/xml/' . $invoice->getName() . '.xml';
        }

        //======== ENVÍO CORRECTO Y ACEPTADO ==========
        if ($res->isSuccess()) {

            //====== GUARDANDO RESPONSE ======
            $data['response_success']   =   $res->isSuccess();
            $data['response_error']     =   $res->getError();
            $data['cdr']                =   $res->getCdrResponse();
            $data['response_cdrZip']    =   $data['cdr'] ? true : false;

            //====== EN CASO HAYA CDR ========
            if ($data['cdr']) {
                $code                                =  $data['cdr']->getCode();
                $data['cdr_response_id']             =   $data['cdr']->getId();
                $data['cdr_response_code']           =   $data['cdr']->getCode();
                $data['cdr_response_description']    =   $data['cdr']->getDescription();
                $data['cdr_response_notes']          =   '|' . implode('|', $data['cdr']->getNotes()) . '|';
                $data['cdr_response_reference']      =   $data['cdr']->getReference();
                $data['message']                     =   $data['cdr_response_description'];

                $util->writeCdr($invoice, $res->getCdrZip(), $dto['tipoDoc'], $dto['company']->files_route, null);

                if ($dto['tipoDoc']   ==  1) {
                    $data['route_cdr']      =   'storage/' . $dto['company']->files_route . '/greenter/facturas/cdr/' . $invoice->getName() . '.zip';
                }

                if ($dto['tipoDoc']   ==  3) {
                    $data['route_cdr']      =   'storage/' . $dto['company']->files_route . '/greenter/boletas/cdr/' . $invoice->getName() . '.zip';
                }
                if ($code == 0) {
                    $data['sunat_status'] = 'ACEPTADO';
                } elseif ($code > 0 && $code < 2000) {
                    $data['sunat_status'] = 'OBSERVADO';
                } else {
                    $data['sunat_status'] = 'RECHAZADO';
                }
            } else {
                $data['message']            =   $dto['serie'] . '-' . $dto['correlativo'] . ' enviado a Sunat, sin CDR recibido';
                $data['sunat_status']       =   'PENDIENTE';
            }

            return $data;
        } else {

            //====== GUARDANDO RESPONSE ======
            $data['sunat_status']       =   'PENDIENTE';
            $data['response_success']   =   $res->isSuccess();
            $data['response_error']     =   $res->getError();

            if ($data['response_error']) {

                $data['response_error_code']        =   $data['response_error']->getCode();
                $data['response_error_message']     =   $data['response_error']->getMessage();
                $message_error                      =   "CÓDIGO: " . $data['response_error']->getCode() . " | DESCRIPCIÓN: " . $data['response_error']->getMessage();
                $data['message']                    =   $message_error;

                /*
                        ================================================================
                        ERROR 1033
                        El comprobante fue registrado previamente con otros datos
                        - Detalle: xxx.xxx.xxx value='ticket: 202413738761966
                        error: El comprobante B001-1704 fue informado anteriormente'

                        ERROR 2223
                        El documento ya fue informado
                    ================================================================
                */
                if ($data['response_error']->getCode() == 1033 || $data['response_error']->getCode() == 2223) {
                    //$data['sunat_status']             =   'RECHAZADO';
                }
            } else {
                $data['message']            =   $dto['serie'] . '-' . $dto['correlativo'] . ' falló el envío a Sunat';
            }

            return $data;
        }
    }
}
