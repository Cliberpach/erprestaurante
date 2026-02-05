<?php

namespace App\Http\Services\Tenant\Invoicing\CreditNote;

use App\Models\Tenant\Sales\Sale\Sale;
use DateTime;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\SaleDetail;
use Greenter\See;
use Greenter\Utils\Util;

class CreditNoteService
{
    public function getDtoSale(int $sale_id)
    {
        $sale   =   Sale::findOrFail($sale_id);
    }

    public function send(array $dto, Util $util, See $see)
    {

        //======== INSTANCIAR OBJETO FACTURA O BOLETA ========
        $note = new Note();

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
        $note
            ->setUblVersion($dto['ublVersion'])
            ->setTipoDoc($dto['tipoDoc'])
            ->setSerie($dto['serie'])
            ->setCorrelativo($dto['correlativo'])
            ->setFechaEmision(new DateTime($dto['fechaEmision']))
            ->setTipDocAfectado($dto['tipDocAfectado']) // Tipo Doc: Factura 01 - Boleta 03
            ->setNumDocfectado($dto['numDocAfectado']) // Factura: Serie-Correlativo
            ->setCodMotivo($dto['codMotivo']) // Catalogo. 09
            ->setDesMotivo($dto['desMotivo'])
            ->setTipoMoneda($dto['tipoMoneda'])
            ->setCompany($util->shared->getCompany())
            ->setClient($client)
            ->setMtoOperGravadas($dto['mtoOperGravadas'])
            ->setMtoIGV($dto['mtoIGV'])
            ->setTotalImpuestos($dto['totalImpuestos'])
            ->setMtoImpVenta($dto['mtoImpVenta']);

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

        $note->setDetails($items)
            ->setLegends([
                (new Legend())
                    ->setCode('1000')
                    ->setValue($dto['legends'])
            ]);

        $res = $see->send($note);

        $data    =   [
            'response_success'          =>  null,
            'response_error'            =>  null,
            'response_error_code'       =>  null,
            'response_error_message'    =>  null,
            'cdr'                       =>  null,
            'response_cdrZip'           =>  null,
            'sunat_status'              =>  null,
            'cdr_response_id'           =>  null,
            'cdr_response_code'         =>  null,
            'cdr_response_description'  =>  null,
            'cdr_response_notes'        =>  null,
            'cdr_response_reference'    =>  null,
            'route_cdr'                 =>  null,
            'route_xml'                 =>  null,
            'message'                   =>  null
        ];

        $util->writeXml($note, $see->getFactory()->getLastXml(), $dto['tipoDoc'] . '-' . $dto['tipDocAfectado'], $dto['company']->files_route, null);

        if ($dto['tipDocAfectado']   ==  '01') {
            $data['route_xml']  =   'storage/' . $dto['company']->files_route . '/greenter/notas_credito_facturas/xml/' . $note->getName() . '.xml';
        }

        if ($dto['tipDocAfectado']   ==  '03') {
            $data['route_xml']  =   'storage/' . $dto['company']->files_route . '/greenter/notas_credito_boletas/xml/' . $note->getName() . '.xml';
        }

        //======== ENVÍO CORRECTO Y ACEPTADO ==========
        if ($res->isSuccess()) {

            //====== GUARDANDO RESPONSE ======
            $data['response_success']   =   $res->isSuccess();
            $data['response_error']     =   $res->getError();
            $data['cdr']                =   $res->getCdrResponse();
            $data['response_cdrZip']    =   $data['cdr'] ? true : false;
            $data['sunat_status']       =   'ENVIADO';

            //====== EN CASO HAYA CDR ========
            if ($data['cdr']) {
                $data['cdr_response_id']             =   $data['cdr']->getId();
                $data['cdr_response_code']           =   $data['cdr']->getCode();
                $data['cdr_response_description']    =   $data['cdr']->getDescription();
                $data['cdr_response_notes']          =   '|' . implode('|', $data['cdr']->getNotes()) . '|';
                $data['cdr_response_reference']      =   $data['cdr']->getReference();
                $data['message']            =   $data['cdr_response_description'];

                $util->writeCdr($note, $res->getCdrZip(), $dto['tipoDoc'] . '-' . $dto['tipDocAfectado'], $dto['company']->files_route, null);

                if ($dto['tipDocAfectado']   ==  '01') {
                    $data['route_cdr']      =   'storage/' . $dto['company']->files_route . '/greenter/notas_credito_facturas/cdr/' . $note->getName() . '.zip';
                }

                if ($dto['tipDocAfectado']   ==  '03') {
                    $data['route_cdr']      =   'storage/' . $dto['company']->files_route . '/greenter/notas_credito_boletas/cdr/' . $note->getName() . '.zip';
                }

                if ($data['cdr']->getCode() == 0) {
                    $data['sunat_status']  =   'ACEPTADO';
                }
            } else {
                $data['message']            =   $dto['serie'] . '-' . $dto['correlativo'] . ' enviado a Sunat, sin CDR recibido';
            }

            return $data;
        } else {

            //====== GUARDANDO RESPONSE ======
            $data['response_success']   =   $res->isSuccess();
            $data['response_error']     =   $res->getError();
            $data['sunat_status']       =   'PENDIENTE';

            if ($data['response_error']) {

                $data['response_error_code']        =   $data['response_error']->getCode();
                $data['response_error_message']     =   $data['response_error']->getMessage();
                $data['sunat_status']               =   'RECHAZADO';
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
                    $data['sunat_status']             =   'RECHAZADO';
                }
            } else {
                $data['message']            =   $dto['serie'] . '-' . $dto['correlativo'] . ' falló el envío a Sunat';
            }

            return $data;
        }
    }
}
