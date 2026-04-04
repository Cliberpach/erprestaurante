<?php

namespace App\Http\Controllers\Tenant\Sales;

use App\Http\Controllers\Controller;
use App\Models\Ventas\Resumen;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Services\Tenant\Sale\Summary\SummaryManager;
use App\Models\Tenant\Sales\Summary\Summary;
use Illuminate\Support\Facades\Response;
use Throwable;

class SummaryController extends Controller
{
    private SummaryManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new SummaryManager();
    }

    public function index()
    {
        return view('sales.summaries.index');
    }

    public function getAll(Request $request)
    {
        $data  =   DB::select('SELECT
                        r.*
                        FROM summaries AS r');

        return DataTables::of($data)->toJson();
    }

    public function getInvoices($fechaComprobantes)
    {
        try {
            //======= SELECCIONAMOS LOS COMPROBANTES DE LA FECHA RESPECTIVA ======
            //===== QUE NO SE ENCUENTREN REGISTRADOS EN NINGÚN DETALLE DE RESUMEN ========
            $comprobantes   =   DB::select('SELECT
                                s.id as documento_id,
                                s.serie as documento_serie,
                                s.correlative as documento_correlativo,
                                "PEN" AS documento_moneda,
                                s.total as documento_total,
                                s.igv_amount as documento_igv,
                                s.igv_percentage as documento_porcentaje_igv,
                                s.subtotal as documento_subtotal,
                                s.customer_document_number as documento_doc_cliente,
                                s.customer_name as documento_nombre_cliente,
                                s.customer_type_document as customer_type_document
                                from sales as s
                                where
                                DATE(s.created_at) = ?
                                AND s.type_sale_code = "03"
                                AND s.status = "ACTIVO"
                                AND s.sunat_status = "PENDIENTE"
                                AND (s.response_success != 1 OR s.response_success is null)
                                AND (s.cdr_response_code != "0" OR s.cdr_response_code is null)
                                AND s.id NOT IN (
                                    SELECT
                                        sd.sale_id
                                    FROM
                                        summaries_details AS sd
                                    WHERE
                                        sd.status = "ACTIVO"
                                )', [$fechaComprobantes]);

            return response()->json([
                'success'  =>  true,
                'fecha'         =>  $fechaComprobantes,
                'comprobantes'  =>  $comprobantes,
                'message'       =>  'COMPROBANTES OBTENIDOS'
            ]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    //======= COMPROBAR SI EXISTE RESUMENES EN EMPRESA NUMERACION FACTURACIÓN ======



    /*
array:2 [ // app\Http\Controllers\Market\Ventas\ResumenController.php:64
  "comprobantes"        => "[{"documento_id":1,"documento_serie":"B001","documento_correlativo":1,"documento_moneda":"PEN","documento_total":"2.000000","documento_igv":"0.305085","documento_porcentaje_igv":"18.000000","documento_subtotal":"1.694915","documento_doc_cliente":"99999999","documento_nombre_cliente":"VARIOS"}]"
  "fecha_comprobantes"  => "2025-02-21"
  "command"             =>  "command"
]
*/
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $instance   =   $this->s_manager->store($request->toArray());

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }

        $res_sunat  =   $this->s_manager->sendSunat($instance->id);
        return response()->json([
            'success' => true,
            'message' => 'Resúmen de boletas registrado con éxito || ' . $res_sunat
        ]);
    }


    public function show($id)
    {
        try {

            $resumen    =   DB::select('SELECT
                            r.*
                            FROM summaries AS r
                            WHERE r.id = ?', [$id]);

            $detalle    =   DB::select('SELECT
                            rd.sale_id  AS documento_id,
                            rd.serie AS documento_serie,
                            rd.correlative AS documento_correlativo,
                            rd.customer_name AS documento_nombre_cliente,
                            rd.customer_document_number AS documento_doc_cliente,
                            rd.total AS documento_total,
                            rd.subtotal AS documento_subtotal,
                            rd.igv AS documento_igv,
                            rd.percentage_igv AS documento_porcentaje_igv
                            FROM summaries_details AS rd
                            WHERE rd.summary_id = ?', [$id]);

            if (count($resumen) === 0) {
                throw new Exception("NO EXISTE EL RESUMEN EN LA BD!!!");
            }

            return response()->json([
                'success'   =>  true,
                'message'   =>  'OPERACIÓN COMPLETADA',
                'resumen'   =>  $resumen[0],
                'detalle'   =>  $detalle
            ]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function sendSunat(int $id)
    {
        try {
            $res_sunat  =   $this->s_manager->sendSunat($id);
            return response()->json(['success' => true, 'message' => $res_sunat]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    /*
array:1 [
  "resumen_id" => "13"
]

RES:
Greenter\Model\Response\StatusResult {#1659
  #code: "0"
  #cdrZip: b"PK\x03\x04\x14\x00\x02\x00\x08\x00Ç\.Z\x00\x00\x00\x00\x02\x00\x00\x00\x00\x00\x00\x00\x06\x00\x00\x00dummy/\x03\x00PK\x03\x04\x14\x00\x02\x00\x08\x00Ç\.Z¢ƒöû4\x04\x00\x00\x1A\x00\x00\x1F\x00\x00\x00R-10470982514-RC-20241107-1.xmlÁW╦n█:\x10¦¸+\x04gQá¢%ÃÅDP\°æán¦ pý4╚Äû\x18ÖìD$ÕG┐■\x0E%Kû\x1C\x05Á\v\x14╔éÜ9sfµpH┬¯ùM\x14\x1A+"$Õý║aƒ[â0Å¹ö\x05Îì¨ýÍ╝l|Ú}p▒p·q\x1CR\x0F+\x00Nëî9ô─Ç`&»\eë`\x0EÃÆJçßêHGã─ú/;░ô,BGzK\x12ag#}g╠V£z─l6▓p\x07ï\x13\x19j*┘│æì:æn╚úê│øì"L½\x00ƒ@IÿÆ{Roß²\x15Ú\x00Ó^-!■;┬~\x10\x08\x12`EÛH}ÏèÑR▒âðz¢>__£s\x11áªeY╚║BÇ±%╬r┤õ8.­Y"y\x0E.mO\x03§\x02\x11Â"!Å\t*Æ@‗"îld¿R░6K\x133▀T\x14z)Æõ}╩äa§nƒ1\x11I╣┘\x07ì«Ùı╬ë7´§júº\x1Fôçö*Ã\x02\v┘─5Eâ#\t▒0┴+êÈø/\e=\x17&╚Ö\x0F&┼@╚|╠k|ÖÑ4;\fV¬þ>ð\x00:HDqDÄÏ\x178f:î°c÷┬{\x1F\f├\x1Dbã\x19Þ\x14Ê▀®V?êZr▀Þç\x01\x17T-úw%░-M\v}yªgÀÏ┘O@Ù\x01Ê\x1A6P╩]Tx4®ı╩k5#.╚ÖÉÏöK▄Âø;╩)y!\x02n\x0Fb╠ºc-\x17\x18┴<\x13ÿ╔\x17."Ö\x19╩ª?ª¡Hö\x0Fúo╩╝·,§ëñÃ\x08\x04äÞ░rwD\x03"ıëèü"geØ×G\x1C&ñÀ©²Ñ¥‗÷═`÷Ý÷ÀX?oF\x1Ez║j╬þ/╠┐\x13v░Yt\x1EÃ¤ô╗Ýw;Yì¢_Å_âºÞv\x1Dv·»│¤¦┴ÔÖL&¾┴ÙÒÂ»\x16ô°÷¾\x1A│ß:©¥vQ9ïÌ\x1FTl\x10î\x1A¬╬Zy"▓êO¸é«Ó¶\x19»dk|\x1C\x10à´ß¿┬uFä·h0«î$■öÐöó▄´dør║OmÙjä\x15╬V:*;¾└|\x07ÎÇox{ËÄ?K\x08\f%■├Óöm,eB─\x03\x11\x14çeï&>Ø¥\x14øre╝wI┤ ÔtÂJt9A^.┌+âÁ÷:┬║■NAo/ƒ7&┘sß¡ÊªÃýM\x1FÅz═s╦Eo¼)nÿH┼ú¦Ý\x02F;ç\x1E:R┤\x06t/:ù¦f╗c]┤[\x19┤­Û&GzïÜV│e┌Âiuwê┬│\x07╬ÓòÞYûô■ù`®=àÕO{N┘6-█┤wI+╬<%░mþóS\x10W\.<¢NIý]\v┌‗0┐Ù¤JM\x15@.Â¸X¿mfKùc\x1F÷ñx─Üªe_└_¾¬¦Ì\x13í¸úrG6|: ]ò*╔<Þ\x00ëÌ+\x0E╬<U8,\x1Aý+à¢eö\x0EÉ÷ÙI\x11\fç¹½ \eÿÚ©wváüÂeëjéðƒÆíCØ§'a>\x11 FJTø`J<BWGþ┤¡VÎ║║lÂÝÍÐ9kRî©ùh\x15‗┴╦k)¥Êí▄i\t)ªCSƒ\x19█Â║ªØ¤ýÌ]Ö´!¸ßÓT\x07;ÁÑ¿\x11æ×áqZÌMh\x00@\x17b°\x14\v╩ìJ× î%6$§╣ü=\x12+ý¾î┤LæwXnc▀\eéÛ█(¶½ï╩─ú1\x05¹æ\eÈ1O┘ù5¬▀\x19T Ëª¸?PK\x01\x02\x00\x00\x14\x00\x02\x00\x08\x00Ç\.Z\x00\x00\x00\x00\x02\x00\x00\x00\x00\x00\x00\x00\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00dummy/PK\x01\x02\x00\x00\x14\x00\x02\x00\x08\x00Ç\.Z¢ƒöû4\x04\x00\x00\x1A\x00\x00\x1F\x00\x00\x00\x00\x00\x00\x00\x01\x00\x00\x00\x00\x00&\x00\x00\x00R-10470982514-RC-20241107-1.xmlPK\x05\x06\x00\x00\x00\x00\x02\x00\x02\x00ü\x00\x00\x00ù\x04\x00\x00\x00\x00"
  #cdrResponse: Greenter\Model\Response\CdrResponse {#1664
    #id: "RC-20241107-1"
    #code: "0"
    #description: "El Resumen diario RC-20241107-1, ha sido aceptado"
    #notes: []
  }
  #success: true
  #error: null
}


Greenter\Model\Response\StatusResult {#1659
  #code: "0127"
  #cdrZip: null
  #cdrResponse: null
  #success: false
  #error: Greenter\Model\Response\Error {#1660
    #code: "0127"
    #message: ""
  }
}
*/
    public function consultar(Request $request)
    {
        try {

            $instance   =   $this->s_manager->consult($request->get('resumen_id'));

            DB::commit();
            return response()->json(['success' => true, 'message' => $instance->last_message]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function getXml($resumen_id)
    {
        $resumen        =   Summary::find($resumen_id);
        $nombreArchivo  =   basename($resumen->route_xml);


        $headers = [
            'Content-Type' => 'text/xml',
        ];

        return Response::download($resumen->route_xml, $nombreArchivo, $headers);
    }

    public function getCdr($resumen_id)
    {
        $resumen        =   Summary::find($resumen_id);
        $nombreArchivo  =   basename($resumen->route_cdr);

        $headers = [
            'Content-Type' => 'text/xml',
        ];

        return Response::download($resumen->route_cdr, $nombreArchivo, $headers);
    }
}
