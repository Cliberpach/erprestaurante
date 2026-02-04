<?php

namespace App\Http\Services\Tenant\Invoicing\GuiaFacturacion;

use App\Greenter\Utils\Util;
use App\Http\Services\Tenant\Invoicing\FacturacionManager;
use Greenter\Api;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Driver;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Despatch\Vehicle;
use Greenter\Model\Response\StatusResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class GuiaFacturacionService extends FacturacionManager
{

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }


//======= ENVIAR GUÍA A SUNAT ======
/*
RESPUESTA:
{#1773 // app\Http\Services\Market\Inventario\GuiaRemision\GuiaRemisionManager.php:135
    +"success": true
    +"message": "GUÍA DE REMISIÓN ENVIADA A SUNAT"
    +"data": {#1784
        +"success": true
        +"ticket": "test-18283663-fdf7-4172-a573-83b7bdd55226"
        +"error": {#1780
            +"code": null
            +"message": null
        }
    }
}
*/
  public function enviarSunat(Request $request):JsonResponse{
    try {

      $util       =   $this->getUtil();
      $api        =   $this->getConfiguracion($util);
      $despatch   =   $this->getDespatch($request);

      //======== CONSTRUYENDO XML Y ENVIANDO A SUNAT ==========
      $res        =   $api->send($despatch);

      //==== GUARDANDO XML ====
      $ruta_xml   =   $this->guardarXml($util,$api,$despatch);


      //==== OBTENER Y GUARDAR TICKET ====
      $ticket                 =   $res->getTicket();
      $res_error              =   $res->getError();
      $data                   =   [];

      $error                  =   [];
      $error['code']          =   $res_error?$res_error->getCode():null;
      $error['message']       =   $res_error?$res_error->getMessage():null;

      $data['success']        =   $res->isSuccess();
      $data['ticket']         =   $ticket;
      $data['error']          =   $error;  //===== CODE Y MESSAGE =====
      $data['ruta_xml']       =   $ruta_xml;
      $data['despatch_name']  =   $despatch->getName();
      $message                =   '';
      $success                =   false;

      //===== VERIFICANDO CONEXIÓN CON SUNAT =======
      if($res->isSuccess()){
        $message  = 'GUÍA REMISIÓN ENVIADA A SUNAT: '.$despatch->getSerie().'-'.$despatch->getCorrelativo();
        $success  = true;
        //$guia->ticket           =   $ticket;
        //$guia->sunat            =   '1';
        //$guia->regularize       =   '0';
        //$guia->despatch_name    =   $despatch->getName();
        //$guia->update();
      } else{
        $message  = 'ERROR AL ENVIAR GUÍA REMISIÓN A SUNAT: '.$despatch->getSerie().'-'.$despatch->getCorrelativo();
        $success  = false;
        //COMO SUNAT NO LO ADMITE VUELVE A SER 0
        //$guia->sunat            =   '0';
        //$guia->regularize       =   '1';
        //$guia->despatch_name    =   $despatch->getName();
        //$guia->update();
      }

      return response()->json(['success'=>$success,'message'=>$message,'data'=>$data]);

    } catch (Throwable $th) {
      return response()->json(['success'=>false,'message'=>$th->getMessage(),'line'=>$th->getLine()]);
    }
  }



  public function getDespatch($request):Despatch{

      $punto_partida      =   (object)$request->get('punto_partida');
      $punto_llegada      =   (object)$request->get('punto_llegada');
      $motivo_traslado    =   $request->get('motivo_traslado');
      $modo_traslado      =   $request->get('modo_traslado');
      $fecha_traslado     =   $request->get('fecha_traslado');
      $peso               =   $request->get('peso');
      $unidad             =   $request->get('unidad');
      $indicador_m1l      =   $request->get('indicador_m1l');
      $transportista      =   (object)$request->get('transportista');
      $conductor          =   (object)$request->get('conductor');
      $vehiculo           =   (object)$request->get('vehiculo');
      $destinatario       =   (object)$request->get('destinatario');
      $version            =   $request->get('version');
      $tipo_doc           =   $request->get('tipo_doc');
      $serie              =   $request->get('serie');
      $correlativo        =   $request->get('correlativo');
      $fecha_emision      =   $request->get('fecha_emision');
      $company            =   (object)$request->get('company');
      $detalle            =   $request->get('detalle');


      $envio = new Shipment();
      $envio
          ->setCodTraslado($motivo_traslado) // Cat.20 - Traslado entre establecimientos de la misma empresa 04 / 01 VENTA
          ->setModTraslado($modo_traslado) // Cat.18 - Transp. Privado 02  / 01 PUBLICO
          ->setFecTraslado(new \DateTime($fecha_traslado))
          ->setPesoTotal($peso)
          ->setUndPesoTotal($unidad)
          ->setPartida((new Direction($punto_partida->ubigeo, $punto_partida->direccion))
            ->setRuc($punto_partida->ruc)
            ->setCodLocal($punto_partida->codigo_local));

      $envio->setLlegada((new Direction($punto_llegada->ubigeo, $punto_llegada->direccion))
          ->setRuc($punto_llegada->ruc)
          ->setCodLocal($punto_llegada->codigo_local));

      if($indicador_m1l === '1'){
        $envio->setIndicadores(['SUNAT_Envio_IndicadorTrasladoVehiculoM1L']);
      }

      //====== TRANSPORTE PÚBLICO ====
      if($modo_traslado === '01'){

        $transp = new Transportist();
        $transp->setTipoDoc(ltrim($transportista->tipo_doc, '0'))
                ->setNumDoc($transportista->nro_doc)
                ->setRznSocial($transportista->razon_social)
                ->setNroMtc($transportista->mtc); //4DIGITOS

        $envio->setTransportista($transp);
      }

      //======== TRANSPORTE PRIVADO =======
      if($modo_traslado === '02'){
        $chofer =   (new Driver())
                    ->setTipo('Principal')  //===== Principal
                    ->setTipoDoc($conductor->tipo_doc)
                    ->setNroDoc($conductor->nro_doc)
                    ->setLicencia($conductor->licencia)
                    ->setNombres($conductor->nombres)
                    ->setApellidos($conductor->apellidos);

        $vehiculoPrincipal  =   (new Vehicle())
                                    ->setPlaca($vehiculo->placa);

        $envio->setChoferes([$chofer])
              ->setVehiculo($vehiculoPrincipal);
      }

      //====== DESTINATARIO =====
      $cliente_despatch   =   new Client();

      $cliente_despatch->setTipoDoc(ltrim($destinatario->tipo_doc, '0'))
                        ->setNumDoc($destinatario->nro_doc)
                        ->setRznSocial($destinatario->razon_social);

      //===== DESPACHO =======
      $_company   =   new Company();
      $_company->setRuc($company->ruc)
                ->setRazonSocial($company->razon_social);

      $despatch = new Despatch();
      $despatch->setVersion($version)
                ->setTipoDoc($tipo_doc)
                ->setSerie($serie)
                ->setCorrelativo($correlativo)
                ->setFechaEmision(new \DateTime($fecha_emision))
                ->setCompany($_company)
                ->setDestinatario($cliente_despatch)
                ->setEnvio($envio);

    $list   =   [];
    foreach ($detalle as $item) {
      $_item  =   (object)$item;

      $detail =   new DespatchDetail();
      $detail->setCantidad($_item->cantidad)
              ->setUnidad($_item->unidad)
              ->setDescripcion($_item->descripcion)
              ->setCodigo($_item->codigo);

      $list[] =   $detail;
    }

    $despatch->setDetails($list);

    return $despatch;
  }

  public function guardarXml(Util $util,Api $api,Despatch $despatch){
    $util->writeXml($despatch, $api->getLastXml(),"09",null);
    return 'storage/greenter/guias_remision/xml/'.$despatch->getName().'.xml';
    //$guia->ruta_xml      =   'storage/greenter/guías_remisión/xml/'.$despatch->getName().'.xml';
    //$guia->update();
  }

  public function guardarCdr(Util $util,StatusResult $res,$despatch_name){
    $util->writeCdr(null, $res->getCdrZip(), "09",$despatch_name);
  }


/*
===== DEVUELVE =======
{#1802 // app\Http\Services\Market\Inventario\GuiaRemision\GuiaSunatService.php:247
  +"success": true
  +"message": "ACEPTADA"
  +"datos": {#1321
    +"status_result_success": true
    +"status_result_error": null
    +"status_result_cdrzip": true
    +"status_result_cdrresponse": {#1779}
    +"status_result_code": "0"
    +"cdr_response": {#1775}
    +"cdr_response_id": "TM01-1"
    +"cdr_response_code": "0"
    +"cdr_response_description": "ACEPTADA"
    +"cdr_response_notes": "|CDR de prueba|"
    +"cdr_response_reference": "https://url-test?hashqr=test"
    +"ruta_cdr": "storage/greenter/guías_remisión/cdr/20609678047-09-TM01-1.zip"
  }
}
*/
  public function consultarSunat(string $ticket,string $despatch_name){
    try {

      $util       =   $this->getUtil();
      $api        =   $this->getConfiguracion($util);

      //======== CONSULTANDO ESTADO DE LA GUÍA =====
      $res = $api->getStatus($ticket);

      //======== response estructura =======
      /*
        code: 99(envío con error)   |   cdrResponse (null o con contenido)
        code: 98(envío en proceso)  |   cdrResponse(aún sin cdr)
        code: 0(envío ok)           |   cdrResponse(con contenido)
      */

      $datos                                = [];

      $datos['status_result_success']       = $res->isSuccess();
      $datos['status_result_error_code']    = $res->getError()?$res->getError()->getCode():null;
      $datos['status_result_error_message'] = $res->getError()?$res->getError()->getMessage():null;
      $datos['status_result_cdrzip']        = $res->getCdrResponse()?true:false;
      $datos['status_result_cdrresponse']   = $res->getCdrResponse();
      $datos['status_result_code']          = $res->getCode();

      $datos['cdr_response']               = null;
      $datos['cdr_response_id']            = null;
      $datos['cdr_response_code']          = null;
      $datos['cdr_response_description']   = null;
      $datos['cdr_response_notes']         = null;
      $datos['cdr_response_reference']     = null;
      $datos['ruta_cdr']                   = null;


      $message    =   null;

      //====== ACEPTADO =======
      if($datos['status_result_code'] == 0){
        $message            =   $despatch_name.' ACEPTADO';

        //======= CDR RECIBIDO ====
        if($datos['status_result_cdrzip']){

          $datos['cdr_response']                = $res->getCdrResponse();
          $datos['cdr_response_id']             = $datos['cdr_response']->getId();
          $datos['cdr_response_code']           = $datos['cdr_response']->getCode();
          $datos['cdr_response_description']    = $datos['cdr_response']->getDescription();
          $datos['cdr_response_notes']          = '|'.implode('|', $datos['cdr_response']->getNotes()).'|';
          $datos['cdr_response_reference']      = $datos['cdr_response']->getReference();

          $this->guardarCdr($util,$res,$despatch_name);
          $datos['ruta_cdr']                   = 'storage/greenter/guías_remisión/cdr/'.$despatch_name.'.zip';

        }
      }

      if($datos['status_result_code'] == 98){
        $message            =   $despatch_name.' EN PROCESO';
        /*
        $guia->sunat            = '1';
        $guia->regularize       = '0';
        $guia->response_code    = $code_estado;
        $guia->update();
        */
      }


      if($datos['status_result_code'] == 99 && $datos['status_result_cdrzip']){
        $message    =   $despatch_name.' ENVÍO CON ERROR CON GENERACIÓN DE CDR';

        //======= CDR RECIBIDO ====
        if($datos['status_result_cdrzip']){

          $datos['cdr_response']                = $res->getCdrResponse();
          $datos['cdr_response_id']             = $datos['cdr_response']->getId();
          $datos['cdr_response_code']           = $datos['cdr_response']->getCode();
          $datos['cdr_response_description']    = $datos['cdr_response']->getDescription();
          $datos['cdr_response_notes']          = '|'.implode('|', $datos['cdr_response']->getNotes()).'|';
          $datos['cdr_response_reference']      = $datos['cdr_response']->getReference();

          $this->guardarCdr($util,$res,$despatch_name);
          $datos['ruta_cdr']                   = 'storage/greenter/guías_remisión/cdr/'.$despatch_name.'.zip';

        }

      }

      if($datos['status_result_code'] == '99' && !$datos['status_result_cdrzip']){
        $message            =   $despatch_name.' ENVÍO CON ERROR SIN GENERACIÓN DE CDR';
      }

      return response()->json(['success'=>true,'message'=>$message,'datos'=>$datos]);

    } catch (Throwable $th) {
      return response()->json(['success'=>false,'message'=>$th->getMessage()]);
    }
  }

}
