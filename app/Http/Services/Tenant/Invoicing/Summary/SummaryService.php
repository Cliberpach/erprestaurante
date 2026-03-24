<?php

namespace App\Http\Services\Tenant\Invoicing\Summary;

use DateTime;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\See;
use Greenter\Utils\Util;

class SummaryService
{
    public function sendSummary(array $dto, Util $util, See $see)
    {
        $details        =   $dto['details'];
        $master_dto     =   $dto['master_dto'];
        $detalles_send  =   [];

        foreach ($details as $item) {

            //====== ESTADOS ======
            //====== 1:NUEVO ==== "2:MODIFICAR" ======= 3:ANULAR =====
            //====== PARA 2,3 DEBE ESTAR PREVIAMENTE INFORMADO EL COMPROBANTE ======
            $detalle = new SummaryDetail();
            $detalle->setTipoDoc($item['tipoDoc'])  //====== SOLO BOLETAS ======
                ->setSerieNro($item['serieNro'])
                ->setEstado($item['estado'])
                ->setClienteTipo($item['clienteTipo'])
                ->setClienteNro($item['clienteNro'])
                ->setTotal((float)$item['total'])
                ->setMtoOperGravadas((float)$item['mtoOperGravadas'])
                ->setMtoIGV($item['mtoIgv']);

            $detalles_send[]    =   $detalle;
        }

        $sum = new Summary();
        //FECHA GENERACIÓN MENOR QUE FECHA RESUMEN
        $sum->setFecGeneracion(new DateTime($master_dto['fecGeneracion']))
            ->setFecResumen(new DateTime($master_dto['fecResumen']))
            ->setCorrelativo($master_dto['correlativo'])
            ->setCompany($util->shared->getCompany())
            ->setDetails($detalles_send);


        $res = $see->send($sum);

        $data    =   [
            'summary_result_success'            =>  null,
            'summary_result_error'              =>  null,
            'summary_result_ticket'             =>  null,
            'summary_name'                      =>  $sum->getName(),
            'response_error'                    =>  null,
            'status_result_code'                =>  null,
            'status_result_success'             =>  null,
            'status_result_error_code'          =>  null,
            'status_result_error_message'       =>  null,
            'sunat_status'              =>  'PENDIENTE',
            'cdr_response_id'           =>  null,
            'cdr_response_code'         =>  null,
            'cdr_response_description'  =>  null,
            'cdr_response_notes'        =>  null,
            'cdr_response_reference'    =>  null,
            'route_cdr'                 =>  null,
            'route_xml'                 =>  null,
            'message'                   =>  null,
            'send_sunat'                =>  null
        ];

        //==== GUARDANDO XML ====
        $util->writeXml($sum, $see->getFactory()->getLastXml(), "RESUMEN", $dto['company']->files_route, null);
        $data['route_xml']  =   'storage/' . $dto['company']->files_route . '/greenter/resumenes/xml/' . $sum->getName() . '.xml';

        //==== VERIFICANDO SI SE ENVIÓ A SUNAT ====
        $envioSunat     =   $res->isSuccess();

        //======== GUARDANDO ESTADO DE ENVIO A SUNAT ======
        $data['summary_result_success']    =   $res->isSuccess();
        $data['summary_result_error']      =   is_string($res->getError()) ? $res->getError() : json_encode($res->getError(), JSON_UNESCAPED_UNICODE);
        $data['summary_result_ticket']     =   $res->getTicket();

        if ($envioSunat) {
            $data['send_sunat']     =   true;
        } else {
            $data['send_sunat']     =   true;
            $data['summary_result']     =   false;
            if ($res->getError()) {
                $error                  =   'CODE: ' . $res->getError()->getCode() . ' - ' . 'MESSAGE: ' . $res->getError()->getMessage();
                $data['response_error'] =   $error;
            }
        }

        return $data;
    }

    public function consult(string $ticket, string $files_route, string $summary_name, Util $util, See $see)
    {
        $res_ticket     =   $see->getStatus($ticket);
        $code_estado    =   $res_ticket->getCode();
        $cdr            =   $res_ticket->getCdrResponse();

        $data    =   [
            'summary_result_success'            =>  null,
            'summary_result_error'              =>  null,
            'response_error'                    =>  null,
            'status_result_code'                =>  null,
            'status_result_success'             =>  null,
            'status_result_error_code'          =>  null,
            'status_result_error_message'       =>  null,
            'sunat_status'              =>  null,
            'cdr_response_id'           =>  null,
            'cdr_response_code'         =>  null,
            'cdr_response_description'  =>  null,
            'cdr_response_notes'        =>  null,
            'cdr_response_reference'    =>  null,
            'route_cdr'                 =>  null,
            'route_xml'                 =>  null,
            'message'                   =>  null,
            'send_sunat'                =>  null
        ];

        //========= GUARDANDO STATUS RESULT RES =====
        $data['status_result_code']            =   $res_ticket->getCode();
        $data['status_result_success']         =   $res_ticket->isSuccess();
        if ($res_ticket->getError()) {
            $data['status_result_error_code']      =   $res_ticket->getError()->getCode();
            $data['status_result_error_message']   =   $res_ticket->getError()->getMessage();
        }

        //====== ENVIO CORRECTO Y CDR RECIBIDO ======
        if ($code_estado == 0) {

            //===== GUARDANDO CDR ======
            $util->writeCdr(null, $res_ticket->getCdrZip(), "RESUMEN", $files_route, $summary_name);
            $data['route_cdr']      =   'storage/' . $files_route . '/greenter/resumenes/cdr/' . $summary_name . '.zip';

            //==== GUARDANDO DATOS DEL CDR ====
            if ($res_ticket->getCdrResponse()) {
                $data['cdr_response_id']           =   $res_ticket->getCdrResponse()->getId();
                $data['cdr_response_code']         =   $res_ticket->getCdrResponse()->getCode();
                $data['cdr_response_description']  =   $res_ticket->getCdrResponse()->getDescription();
                $data['cdr_response_notes']        =   implode(" | ", $res_ticket->getCdrResponse()->getNotes());
            }

            $data['message']    =   "ENVÍO CORRECTO Y CDR RECIBIDO";
            $data['sunat_status']   =   'ACEPTADO';
        }

        //===== ENVÍO CON ERRORES Y CDR RECIBIDO =====
        if ($code_estado == 99 && $cdr) {

            //===== GUARDANDO CDR ======
            $util->writeCdr(null, $res_ticket->getCdrZip(), "RESUMEN", $files_route, $summary_name);
            $data['route_cdr']      =   'storage/' . $files_route . '/greenter/resumenes/cdr/' . $summary_name . '.zip';

            //==== GUARDANDO DATOS DEL CDR ====
            $data['cdr_response_id']           =   $res_ticket->getCdrResponse()->getId();
            $data['cdr_response_code']         =   $res_ticket->getCdrResponse()->getCode();
            $data['cdr_response_description']  =   $res_ticket->getCdrResponse()->getDescription();
            $data['cdr_response_notes']        =   implode(" | ", $res_ticket->getCdrResponse()->getNotes());

            $data['message']        =   "ENVÍO CON ERRORES Y CDR RECIBIDO";
            $data['sunat_status']   =   'RECHAZADO';
        }

        //===== ENVÍO CON ERRORES Y SIN CDR =====
        if ($code_estado == 99 && !$cdr) {
            $data['message']    =   "ENVÍO CON ERRORES,SIN CDR";
            $data['sunat_status']   =   'RECHAZADO';
        }

        //======= EN PROCESO =======
        if ($code_estado == 98) {
            //====== GUARDANDO ESTADO DEL TICKET ====
            $data['message']    =   "ENVÍO EN PROCESO";
            $data['sunat_status']   =   'EN PROCESO';
        }

        //======== CONSULTA FALSE ======
        if (!$res_ticket->isSuccess() && $code_estado != 0 && $code_estado != 99 && $code_estado != 98) {
            $data['message']                =   $res_ticket->getError()->getCode() . '-' . $res_ticket->getError()->getMessage();
            $data['sunat_status']   =   'EN PROCESO';
        }

        return $data;
    }
}
