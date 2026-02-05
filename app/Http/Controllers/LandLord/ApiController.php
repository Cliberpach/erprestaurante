<?php

namespace App\Http\Controllers\LandLord;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Tenant\Orders\Order;
use App\Models\Tenant\Sales\Sale\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function apiRuc($ruc)
    {
        $url = "https://apiperu.dev/api/ruc/" . $ruc;
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $token = 'c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d';
        $response = $client->get($url, [
            'http_errors' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ],
        ]);

        $estado = $response->getStatusCode();
        $data = $response->getBody()->getContents();
        return $data;
    }


    /*
    TRUE DNI:
    {#2048 // app\Http\Controllers\Tenant\CustomerController.php:143
        +"success": true
        +"data": {#2044
            +"numero": "75608753"
            +"nombre_completo": "ALVA LUJAN, LUIS DANIEL"
            +"nombres": "LUIS DANIEL"
            +"apellido_paterno": "ALVA"
            +"apellido_materno": "LUJAN"
            +"codigo_verificacion": 9
            +"ubigeo_sunat": ""
            +"ubigeo": array:3 [
            0 => null
            1 => null
            2 => null
            ]
            +"direccion": ""
        }
        +"time": 0.046104907989502
        +"source": "apiperu.dev"
    }

    FALSE DNI:
    {#2048 // app\Http\Controllers\Tenant\CustomerController.php:143
        +"success": false
        +"message": "No se encontraron registros"
        +"time": 0.23091197013855
        +"source": "apiperu.dev"
    }

TRUE RUC:
{
data:{
    direccion: "CAR. PANAMERICANA SUR NRO. 241 PANAMERICANA SUR",…}
    condicion:"HABIDO"
    departamento:"ICA"
    direccion:"CAR. PANAMERICANA SUR NRO. 241  PANAMERICANA SUR"
    direccion_completa:"CAR. PANAMERICANA SUR NRO. 241  PANAMERICANA SUR, ICA - PISCO - PARACAS"
    distrito:"PARACAS"
    es_agente_de_percepcion:"SI"
    es_agente_de_percepcion_combustible:"NO"
    es_agente_de_retencion:"SI"
    es_buen_contribuyente:"NO"
    estado:"ACTIVO"
    nombre_o_razon_social:"CORPORACION ACEROS AREQUIPA S.A."
    provincia:"PISCO"
    ruc:"20370146994"
    ubigeo:["11", "1105", "110505"]
    ubigeo_sunat: "110505"
}
message:"OPERACIÓN COMPLETADA"
success: true
}
*/
    public function apiDni($dni)
    {

        $url = "https://apiperu.dev/api/dni/" . $dni;
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $token = 'c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d';
        $response = $client->get($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token}",
            ],
        ]);
        $estado = $response->getStatusCode();
        $data = $response->getBody()->getContents();

        return $data;
    }

    //Modificado por DANIEL-ALVA: todas las funciones fueron agregadas por DANIEL-ALVA
    function ListarPedidosPendientesImprimir()
    {
        $comandas = Order::where('status', '<>', 'ANULADO')
            ->where('pending_order_printing', '=', 'SI')
            ->select('id AS idpedido')
            ->orderby('id', 'asc')
            ->get();

        $pedidos = Order::where('status', '<>', 'ANULADO')
            ->where('pending_print', '=', 'SI')
            ->select('id AS idpedido')
            ->orderby('id', 'asc')
            ->get();

        $cpe = Sale::where('status', '!=', 'ANULADO')
            ->where('sunat_status', '<>', 'ANULADO')
            ->where('pending_print', 'SI')
            ->select('id AS idrecibo')
            ->orderby('id', 'asc')
            ->get();

        return [
            'comandas' => $comandas,
            'pedidos' => $pedidos,
            'cpe' => $cpe
        ];
    }

    function ObtenerPedido_PorCodigo($idpedido)
    {
        $nombreImp = 'PDF24 PDF';
        // $area_impresion = AreaImpresion::where('codigo', 'caja')->first();
        // if ($area_impresion != null){
        //     $id = $area_impresion->id;
        //     $datos = DB::select('select nombre from area_impresion_detalle where idarea=? limit 1', [$id]);
        //     $datos = json_decode(json_encode($datos), true);
        //     foreach($datos as $dd){
        //         $nombreImp = $dd['nombre'];
        //         break;
        //     }
        // }

        $company    =   Company::findOrFail(1);
        $empresa = $company->business_name;
        $ruc = $company->ruc;
        $direccionEmpresa = $company->fiscal_address;
        $telefonoEmpresa = $company->phone;
        $emailEmpresa = $company->email;

        $pedido     =   Order::findOrFail($idpedido);
        $detalles   =   $pedido->getDetails();

        /*$detalles = DPedido::where('idpedido', '=', $idpedido)
            ->where('estado_delete', '=', '1')
            ->get();*/

        $arrayPedido = array();
        $fila = [
            'nombrecomercial' => $empresa,
            'ruc' => $ruc,
            'direccion' => $direccionEmpresa,
            'correo' => 'Email: ' . $emailEmpresa,
            'telefono' => '+51 ' . $telefonoEmpresa,

            'idpedido' => $pedido->id,
            'fecha' => $pedido->created_at->format('d/m/Y h:i:s a'),
            'mesero' => 'MESERO: ' . $pedido->creator_user_name,
            'mesa' => 'Nº MESA - ' . $pedido->table->name,
            'total' => $pedido->total,
            'observacion' => $item->observation ?? 'Sin observaciones',
            'im_nombre' => $nombreImp
        ];
        array_push($arrayPedido, $fila);


        $arrayDetalles = array();
        foreach ($detalles as $det) {
            $fila = [
                'cantidad' => $det->quantity,
                'descripcion' => $det->item_name,
                'precio' => $det->sale_price,
                'importe' => $det->total
            ];
            array_push($arrayDetalles, $fila);
        }

        return [
            'pedido' => $arrayPedido,
            'detalles' => $arrayDetalles
        ];
    }

    function ActualizarPedidosPendientesImprimir($idpedido)
    {
        $pedido = Order::findOrFail($idpedido);
        if ($pedido) {
            $pedido->pending_print      =   'NO';
            $pedido->date_pending_print =   now();
            $pedido->save();
        }
    }

    function ObtenerRecibo_PorCodigo($idrecibo)
    {
        //cocina - bebidas - caja
        $nombreImp = 'PDF24 PDF';
        // $area_impresion = AreaImpresion::where('codigo', 'caja')->first();
        // if ($area_impresion != null){
        //     $id = $area_impresion->id;
        //     $datos = DB::select('select nombre from area_impresion_detalle where idarea=? limit 1', [$id]);
        //     $datos = json_decode(json_encode($datos), true);
        //     foreach($datos as $dd){
        //         $nombreImp = $dd['nombre'];
        //         break;
        //     }
        // }

        $company    =   Company::findOrFail(1);
        $empresa = $company->business_name;
        $ruc = $company->ruc;
        $direccionEmpresa = $company->fiscal_address;
        $telefonoEmpresa = $company->phone;
        $emailEmpresa = $company->email;

        $recibo     = Sale::where('id', '=', $idrecibo)->first();
        $detalles   = $recibo->getDetails();

        $CodPedido = -1;
        foreach ($detalles as $dd) {
            if ($CodPedido == -1) $CodPedido = $dd->order_id;
        }

        $arrayRecibo = array();
        $fila = [
            'nombrecomercial' => $empresa,
            'ruc' => $ruc,
            'direccion' => $direccionEmpresa,
            'correo' => 'Email: ' . $emailEmpresa,
            'telefono' => '+51 ' . $telefonoEmpresa,

            'idrecibo' => $recibo->id,
            'fecha' => $recibo->created_at->format('d/m/Y h:i:s a'),
            'tipo' => $recibo->type_sale_name,
            'serie' => $recibo->serie,
            'correlativo' => $recibo->correlative,
            'nrodoc' => $recibo->customer_document_number,
            'cliente' => $recibo->customer_name,
            'direccionCliente' => $recibo->customer_address,
            'subtotal' => $recibo->subtotal,
            'igv' => $recibo->igv_amount,
            'monto_total' => $recibo->total,
            'total' => $recibo->total,
            'descuento' => $recibo->discount,
            'hash' => $recibo->serie . '-' . $recibo->correlative,
            'im_nombre' => $nombreImp
        ];
        array_push($arrayRecibo, $fila);

        $detalles   =   Order::where('id', $CodPedido)->first()->getDetails();
        // $detalles = DPedido::where('idpedido', '=', $CodPedido)
        //     ->where('estado_delete', '=', '1')
        //     ->get();

        $arrayDetalles = array();
        foreach ($detalles as $det) {
            $fila = [
                'cantidad' => $det->quantity,
                'descripcion' => $det->item_name,
                'precio' => $det->sale_price,
                'importe' => $det->total
            ];
            array_push($arrayDetalles, $fila);
        }

        return [
            'recibo' => $arrayRecibo,
            'detalles' => $arrayDetalles
        ];
    }

    function ActualizarReciboPendienteImprimir($idrecibo)
    {
        $recibo = Sale::findOrFail($idrecibo);
        if ($recibo != null) {
            $recibo->pending_print = 'NO';
            $recibo->date_pending_print =   now();
            $recibo->save();
        }
    }

    function ObtenerComanda_PorCodigo($idpedido)
    {
        // cocina - bebidas - caja
        $nombreImpComidas = 'PDF24 PDF';
        // $area_impresion_cocina = AreaImpresion::where('codigo', 'cocina')->first();
        // if ($area_impresion_cocina != null){
        //     $id = $area_impresion_cocina->id;
        //     $datos = DB::select('select nombre from area_impresion_detalle where idarea=? limit 1', [$id]);
        //     $datos = json_decode(json_encode($datos), true);
        //     foreach($datos as $dd){
        //         $nombreImpComidas = $dd['nombre'];
        //         break;
        //     }
        // }

        // cocina - bebidas - caja
        $nombreImpBebidas = 'PDF24 PDF';
        // $area_impresion_bebida = AreaImpresion::where('codigo', 'bebidas')->first();
        // if ($area_impresion_bebida != null){
        //     $id = $area_impresion_bebida->id;
        //     $datos = DB::select('select nombre from area_impresion_detalle where idarea=? limit 1', [$id]);
        //     $datos = json_decode(json_encode($datos), true);
        //     foreach($datos as $dd){
        //         $nombreImpBebidas = $dd['nombre'];
        //         break;
        //     }
        // }

        $nombreImpComidas = "PLATO";
        $nombreImpBebidas = "BEBIDA";

        $company    =   Company::findOrFail(1);
        $empresa = $company->business_name;
        $ruc = $company->ruc;
        $direccionEmpresa = $company->fiscal_address;
        $telefonoEmpresa = $company->phone;
        $emailEmpresa = $company->email;

        $pedido = Order::where('id', $idpedido)->first();

        $arrayPedido = array();
        $modo = '';
        if ($pedido->order_print_mode == 'TODO') $modo = 'COCINA - MOSTRADOR';
        else if ($pedido->order_print_mode == 'PLATO') $modo = 'COCINA';
        else if ($pedido->order_print_mode == 'BEBIDA') $modo = 'MOSTRADOR';
        else if ($pedido->order_print_mode == 'PARCIAL') $modo = 'COCINA - MOSTRADOR';

        $fila = [
            'nombrecomercial' => $empresa,
            'ruc' => $ruc,
            'direccion' => $direccionEmpresa,
            'correo' => 'Email: ' . $emailEmpresa,
            'telefono' => '+51 ' . $telefonoEmpresa,

            'idpedido' => $pedido->id,
            'fecha' => $pedido->created_at->format('d/m/Y h:i:s a'),
            'mesero' => 'MESERO: ' . $pedido->creator_user_name,
            'mesa' => 'Nº MESA - ' . $pedido->table->name,
            'total' => $pedido->total,
            'observacion' => $pedido->observation ?? 'Sin observaciones',
            'estado' => $pedido->hasSale() ? 'Pagado' : 'Confirmado',
            'modoImpresionComanda' => $modo
        ];
        array_push($arrayPedido, $fila);


        //----------------------------------------------------------------------------
        //------------------ obtener pedido - y verificar las bebidas ----------------
        //----------------------------------------------------------------------------
        $modoImpresionComanda = $pedido->order_print_mode;
        $details    =   $pedido->getDetails()->where('detail_printed', 'NO');
        $bebidas = array();
        $platos = array();

        if ($modoImpresionComanda == 'PARCIAL') {
            $bebidas    =   $details->where('item_type', 'PRODUCTO')->sortByDesc('created_at')->values();

            /*$bebidas = $objPedido
                ->DPedidoPendiente()
                ->where('nombre_tabla', 'producto')
                ->where('DPED_Impreso', 'NO')
                ->orderBy('created_at', 'desc')
                ->get();*/

            foreach ($bebidas as $det) {
                DB::update(
                    "UPDATE orders_products
                    SET detail_printed='SI',updated_at = ?
                    WHERE order_id=?
                    AND product_id=?
                    AND id=? ",
                    [
                        now(),
                        $idpedido,
                        $det->item_id,
                        $det->id
                    ]
                );
            }

            $platos    =   $details->where('item_type', 'PLATO')->sortByDesc('created_at')->values();
            /*$platos = $objPedido
                ->DPedidoPendiente()
                ->where('nombre_tabla', 'platos')
                ->where('DPED_Impreso', 'NO')
                ->orderBy('created_at', 'desc')
                ->get();*/

            foreach ($platos as $det) {
                DB::update(
                    "UPDATE orders_dishes
                    SET detail_printed='SI',updated_at = ?
                    WHERE order_id=?
                    AND dish_id=?
                    AND id=? ",
                    [
                        now(),
                        $idpedido,
                        $det->item_id,
                        $det->id
                    ]
                );
                // DB::update(
                //     " UPDATE d_pedido
                //     SET DPED_Impreso='SI'
                //     WHERE idpedido=?
                //     AND idplato=?
                //     AND nombre_tabla=?
                //     AND correlativo=? ",
                //     [
                //         $idpedido,
                //         $det->idplato,
                //         $det->nombre_tabla,
                //         $det->correlativo
                //     ]
                // );
            }
        } else {
            if ($modoImpresionComanda == 'TODO' || $modoImpresionComanda == 'BEBIDA') {
                $bebidas    =   $details->where('item_type', 'PRODUCTO')->sortByDesc('created_at')->values();

                /*$bebidas = $objPedido
                    ->DPedidoPendiente()
                    ->where('nombre_tabla', 'producto')
                    //->where('DPED_Impreso', 'NO')
                    ->orderBy('created_at', 'desc')
                    ->get();*/
            }

            if ($modoImpresionComanda == 'TODO' || $modoImpresionComanda == 'PLATO') {
                $platos    =   $details->where('item_type', 'PLATO')->sortByDesc('created_at')->values();

                /*$platos = $objPedido
                    ->DPedidoPendiente()
                    ->where('nombre_tabla', 'platos')
                    //->where('DPED_Impreso', 'NO')
                    ->orderBy('created_at', 'desc')
                    ->get();*/
            }
        }

        $arrayDetalles = array();
        foreach ($bebidas as $det) {
            $nombreImp = '';
            if ($det->item_type == 'PLATO') $nombreImp = $nombreImpComidas;
            else $nombreImp = $nombreImpBebidas;

            $fila = [
                'im_nombre' => $nombreImp,
                'cantidad' => $det->quantity,
                'descripcion' => $det->item_name,
                'observacion' => $det->observation ?? 'Sin observaciones',
                'fecha' => Carbon::parse($det->created_at)->format('d/m/Y h:i:s a'),
            ];
            array_push($arrayDetalles, $fila);
        }

        foreach ($platos as $det) {
            $nombreImp = '';
            if ($det->item_type == 'PLATO') $nombreImp = $nombreImpComidas;
            else $nombreImp = $nombreImpBebidas;

            $fila = [
                'im_nombre' => $nombreImp,
                'cantidad' => $det->quantity,
                'descripcion' => $det->item_name,
                'observacion' => $det->observation ?? 'Sin observaciones',
                'fecha' => Carbon::parse($det->created_at)->format('d/m/Y h:i:s a'),
            ];
            array_push($arrayDetalles, $fila);
        }

        return [
            'pedido' => $arrayPedido,
            'detalles' => $arrayDetalles
        ];
    }

    function ActualizarComandaPendienteImprimir($idpedido)
    {
        $pedido = Order::findOrFail($idpedido);
        if ($pedido != null) {
            $pedido->pending_order_printing = 'NO';
            $pedido->date_pending_order_print =   now();
            $pedido->save();
        }
    }
}
