<?php

namespace App\Http\Controllers\LandLord;

use App\Http\Controllers\Controller;

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




    //Modificado por JC: todas las funciones fueron agregadas por JC
    function ListarPedidosPendientesImprimir()
    {
        $comandas = Pedido::where('estado_delete', '=', '1')
            ->where('ImpresionPendienteComanda', '=', 'SI')
            ->select('idpedido')
            ->orderby('idpedido', 'asc')
            ->get();

        $pedidos = Pedido::where('estado_delete', '=', '1')
            ->where('ImpresionPendiente', '=', 'SI')
            ->select('idpedido')
            ->orderby('idpedido', 'asc')
            ->get();

        $cpe = Recibo::where('estado', '!=', 'ANULADO')
            ->where('ImpresionPendiente', '=', 'SI')
            ->select('idrecibo')
            ->orderby('idrecibo', 'asc')
            ->get();

        return [
            'comandas' => $comandas,
            'pedidos' => $pedidos,
            'cpe' => $cpe
        ];
    }

    function ObtenerPedido_PorCodigo($idpedido)
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

        $empresa = DB::table('empresa')->count() == 0 ? 'SISCOM ' : DB::table('empresa')->first()->nombre;
        $ruc = DB::table('empresa')->count() == 0 ? '- ' : DB::table('empresa')->first()->ruc;
        $direccionEmpresa = DB::table('empresa')->count() == 0 ? '- ' : DB::table('empresa')->first()->direccion;
        $telefonoEmpresa = DB::table('empresa')->count() == 0 ? '-' : DB::table('empresa')->first()->telefono;
        $emailEmpresa = DB::table('empresa')->count() == 0 ? '-' : DB::table('empresa')->first()->email;

        $pedido = Pedido::where('idpedido', '=', $idpedido)->get();
        $detalles = DPedido::where('idpedido', '=', $idpedido)
            ->where('estado_delete', '=', '1')
            ->get();

        $arrayPedido = array();
        foreach ($pedido as $item) {
            $fila = [
                'nombrecomercial' => $empresa,
                'ruc' => $ruc,
                'direccion' => $direccionEmpresa,
                'correo' => 'Email: ' . $emailEmpresa,
                'telefono' => '+51 ' . $telefonoEmpresa,

                'idpedido' => $item->idpedido,
                'fecha' => $item->fecha->format('d/m/Y h:i:s a'),
                'mesero' => 'MESERO: ' . $item->Trabajador->Persona->nombres_apellidos(),
                'mesa' => 'Nº MESA - ' . $item->Reserva->Mesa->nromesa,
                'total' => $item->Total2($item->DPedido),
                'observacion' => $item->GetObservacion(),
                'im_nombre' => $nombreImp
            ];
            array_push($arrayPedido, $fila);
        }

        $arrayDetalles = array();
        foreach ($detalles as $det) {
            $fila = [
                'cantidad' => $det->cantidad,
                'descripcion' => $det->producto->nombre,
                'precio' => $det->p_venta,
                'importe' => $det->cantidad * $det->p_venta
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
        $pedido = Pedido::findorfail($idpedido);
        if ($pedido != null) {
            $pedido->ImpresionPendiente = 'NO';
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

        $empresa = DB::table('empresa')->count() == 0 ? 'SISCOM ' : DB::table('empresa')->first()->nombre;
        $ruc = DB::table('empresa')->count() == 0 ? '- ' : DB::table('empresa')->first()->ruc;
        $direccionEmpresa = DB::table('empresa')->count() == 0 ? '- ' : DB::table('empresa')->first()->direccion;
        $telefonoEmpresa = DB::table('empresa')->count() == 0 ? '-' : DB::table('empresa')->first()->telefono;
        $emailEmpresa = DB::table('empresa')->count() == 0 ? '-' : DB::table('empresa')->first()->email;

        $recibo = Recibo::where('idrecibo', '=', $idrecibo)->get();
        $detalles = DRecibo::where('idrecibo', '=', $idrecibo)
            ->get();

        $CodPedido = -1;
        foreach ($detalles as $dd) {
            if ($CodPedido == -1) $CodPedido = $dd->idpedido;
        }

        $arrayRecibo = array();
        foreach ($recibo as $item) {
            $fila = [
                'nombrecomercial' => $empresa,
                'ruc' => $ruc,
                'direccion' => $direccionEmpresa,
                'correo' => 'Email: ' . $emailEmpresa,
                'telefono' => '+51 ' . $telefonoEmpresa,

                'idrecibo' => $item->idrecibo,
                'fecha' => $item->fecha->format('d/m/Y h:i:s a'),
                'tipo' => $item->tipo,
                'serie' => $item->serie,
                'correlativo' => $item->correlativo,
                'nrodoc' => ($item->Cliente->ClientePersona != null) ? $item->Cliente->ClientePersona->Persona->dni : $item->Cliente->ClienteRuc->ruc,
                'cliente' => ($item->Cliente->ClientePersona != null) ? $item->Cliente->ClientePersona->Persona->nombres_apellidos() : $item->Cliente->ClienteRuc->nombre_comercial,
                'direccionCliente' => ($item->Cliente->ClientePersona != null) ? $item->Cliente->ClientePersona->Persona->direccion : $item->Cliente->ClienteRuc->direccion,
                'subtotal' => $item->subtotal,
                'igv' => $item->total_igv,
                'monto_total' => $item->monto_total,
                'total' => $item->total,
                'descuento' => $item->descuento,
                'hash' => $item->hash,
                'im_nombre' => $nombreImp
            ];
            array_push($arrayRecibo, $fila);
        }

        $detalles = DPedido::where('idpedido', '=', $CodPedido)
            ->where('estado_delete', '=', '1')
            ->get();

        $arrayDetalles = array();
        foreach ($detalles as $det) {
            $fila = [
                'cantidad' => $det->cantidad,
                'descripcion' => $det->producto->nombre,
                'precio' => $det->p_venta,
                'importe' => $det->cantidad * $det->p_venta
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
        $recibo = Recibo::findorfail($idrecibo);
        if ($recibo != null) {
            $recibo->ImpresionPendiente = 'NO';
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

        $empresa = DB::table('empresa')->count() == 0 ? 'SISCOM ' : DB::table('empresa')->first()->nombre;
        $ruc = DB::table('empresa')->count() == 0 ? '- ' : DB::table('empresa')->first()->ruc;
        $direccionEmpresa = DB::table('empresa')->count() == 0 ? '- ' : DB::table('empresa')->first()->direccion;
        $telefonoEmpresa = DB::table('empresa')->count() == 0 ? '-' : DB::table('empresa')->first()->telefono;
        $emailEmpresa = DB::table('empresa')->count() == 0 ? '-' : DB::table('empresa')->first()->email;

        $pedido = Pedido::where('idpedido', '=', $idpedido)->get();

        $arrayPedido = array();
        foreach ($pedido as $item) {
            $modo = '';
            if ($item->ModoImpresionComanda == 'TODO') $modo = 'COCINA - MOSTRADOR';
            else if ($item->ModoImpresionComanda == 'PLATO') $modo = 'COCINA';
            else if ($item->ModoImpresionComanda == 'BEBIDA') $modo = 'MOSTRADOR';
            else if ($item->ModoImpresionComanda == 'PARCIAL') $modo = 'COCINA - MOSTRADOR';

            $fila = [
                'nombrecomercial' => $empresa,
                'ruc' => $ruc,
                'direccion' => $direccionEmpresa,
                'correo' => 'Email: ' . $emailEmpresa,
                'telefono' => '+51 ' . $telefonoEmpresa,

                'idpedido' => $item->idpedido,
                'fecha' => $item->fecha->format('d/m/Y h:i:s a'),
                'mesero' => 'MESERO: ' . $item->Trabajador->Persona->nombres_apellidos(),
                'mesa' => 'Nº MESA - ' . $item->Reserva->Mesa->nromesa,
                'total' => $item->Total2($item->DPedido),
                'observacion' => $item->GetObservacion(),
                'estado' => $item->estado,
                'modoImpresionComanda' => $modo
            ];
            array_push($arrayPedido, $fila);
        }

        //----------------------------------------------------------------------------
        //------------------ obtener pedido - y verificar las bebidas ----------------
        //----------------------------------------------------------------------------
        $objPedido = Pedido::findorfail($idpedido);
        $modoImpresionComanda = $objPedido->ModoImpresionComanda;

        $bebidas = array();
        $platos = array();

        if ($modoImpresionComanda == 'PARCIAL') {
            $bebidas = $objPedido
                ->DPedidoPendiente()
                ->where('nombre_tabla', 'producto')
                ->where('DPED_Impreso', 'NO')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($bebidas as $det) {
                DB::update(
                    " UPDATE d_pedido SET DPED_Impreso='SI' WHERE idpedido=? AND idplato=? AND nombre_tabla=? AND correlativo=? ",
                    [$idpedido, $det->idplato, $det->nombre_tabla, $det->correlativo]
                );
            }

            $platos = $objPedido
                ->DPedidoPendiente()
                ->where('nombre_tabla', 'platos')
                ->where('DPED_Impreso', 'NO')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($platos as $det) {
                DB::update(
                    " UPDATE d_pedido SET DPED_Impreso='SI' WHERE idpedido=? AND idplato=? AND nombre_tabla=? AND correlativo=? ",
                    [$idpedido, $det->idplato, $det->nombre_tabla, $det->correlativo]
                );
            }
        } else {
            if ($modoImpresionComanda == 'TODO' || $modoImpresionComanda == 'BEBIDA') {
                $bebidas = $objPedido
                    ->DPedidoPendiente()
                    ->where('nombre_tabla', 'producto')
                    //->where('DPED_Impreso', 'NO')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            if ($modoImpresionComanda == 'TODO' || $modoImpresionComanda == 'PLATO') {
                $platos = $objPedido
                    ->DPedidoPendiente()
                    ->where('nombre_tabla', 'platos')
                    //->where('DPED_Impreso', 'NO')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        $arrayDetalles = array();
        foreach ($bebidas as $det) {
            $nombreImp = '';
            if ($det->nombre_tabla == 'platos') $nombreImp = $nombreImpComidas;
            else $nombreImp = $nombreImpBebidas;

            $fila = [
                'im_nombre' => $nombreImp,
                'cantidad' => $det->cantidad,
                'descripcion' => $det->producto->nombre,
                'observacion' => $det->descripcion == null ? 'Sin observaciones' : $det->descripcion,
                'fecha' => $det->created_at->format('d/m/Y h:i:s a')
            ];
            array_push($arrayDetalles, $fila);
        }

        foreach ($platos as $det) {
            $nombreImp = '';
            if ($det->nombre_tabla == 'platos') $nombreImp = $nombreImpComidas;
            else $nombreImp = $nombreImpBebidas;

            $fila = [
                'im_nombre' => $nombreImp,
                'cantidad' => $det->cantidad,
                'descripcion' => $det->producto->nombre,
                'observacion' => $det->descripcion == null ? 'Sin observaciones' : $det->descripcion,
                'fecha' => $det->created_at->format('d/m/Y h:i:s a')
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
        $pedido = Pedido::findorfail($idpedido);
        if ($pedido != null) {
            $pedido->ImpresionPendienteComanda = 'NO';
            $pedido->save();
        }
    }
}
