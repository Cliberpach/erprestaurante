<?php

/*
============== MÉTODO ENVIAR SUNAT() ===========
motivo_traslado
modo_traslado
fecha_traslado
peso
unidad

PUNTO PARTIDA: distrito_id,direccion,ruc,codigo_local
PUNTO LLEGADA:
 ->CON RUC:distrito_id,direccion,ruc,codigo_local.
 ->CON DNI:distrito_id,direccion. 

categoria_M1L: 1 o 0

MODOS TRASLADO: 
->TRANSPORTE PUBLICO: 
(TRANSPORTISTA) 
*tipo_doc
*nro_doc
*razon_social
*mtc

->TRANSPORTE PRIVADO:
(CONDUCTOR)
*TIPO: PRINCIPAL
*TIPO DOC
*NRO DOC
*LICENCIA
*NOMBRES
*APELLIDOS

(VEHICULO)
*PLACA

MOTIVOS TRASLADO:
->TRASLADO INTERNO (04) (RUC SIEMPRE)
*TIPO DOC
*NRO DOC
*RAZON SOCIAL

->VENTA (01) (DNI O RUC)
*TIPO DOC
*NRO DOC
*RAZON SOCIAL

===== DESPACHO ========
-VERSION (2022)
-TIPO DOC: 09
-SERIE
-CORRELATIVO
-FECHA EMISIÓN
-COMPANY:
 ->RUC
 ->RAZON SOCIAL

==== DETALLE ======
CANTIDAD
UNIDAD
DESCRIPCION
CODIGO
*/



/*
============== MÉTODO ENVIAR GETDESPATCH() ===========
Greenter\Model\Despatch\Despatch {#1777 // app\Http\Services\Facturacion\GuiaFacturacionService.php:94
  -version: "2022"
  -tipoDoc: "09"
  -serie: "TM01"
  -correlativo: "1"
  -observacion: null
  -fechaEmision: DateTime @1746888685 {#1778
    date: 2025-05-10 09:51:25.0 America/Lima (-05:00)
  }
  -company: 
Greenter\Model\Company
\
Company
 {#1776
    -ruc: "20609678047"
    -razonSocial: "CORPORACION CHAGUALITO S.A.C."
    -nombreComercial: null
    -address: null
    -email: null
    -telephone: null
  }
  -destinatario: 
Greenter\Model\Client
\
Client
 {#1770
    -tipoDoc: "06"
    -numDoc: "20609678047"
    -rznSocial: "CORPORACION CHAGUALITO S.A.C."
    -address: null
    -email: null
    -telephone: null
  }
  -tercero: null
  -comprador: null
  -envio: 
Greenter\Model\Despatch
\
Shipment
 {#1765
    -codTraslado: "04"
    -desTraslado: null
    -sustentoPeso: null
    -indTransbordo: null
    -indicadores: null
    -pesoItems: null
    -pesoTotal: 1.0
    -undPesoTotal: "KGM"
    -numBultos: null
    -modTraslado: "01"
    -fecTraslado: DateTime @1746853200 {#1764
      date: 2025-05-10 00:00:00.0 America/Lima (-05:00)
    }
    -numContenedor: null
    -contenedores: null
    -codPuerto: null
    -puerto: null
    -aeropuerto: null
    -transportista: 
Greenter\Model\Despatch
\
Transportist
 {#1769
      -tipoDoc: "06"
      -numDoc: "20370146999"
      -rznSocial: "CORPORACION ACEROS AREQUIPA S.A."
      -nroMtc: "ZCS123"
      -placa: null
      -choferTipoDoc: null
      -choferDoc: null
    }
    -vehiculo: null
    -choferes: null
    -llegada: 
Greenter\Model\Despatch
\
Direction
 {#1767
      -ubigueo: "010503"
      -direccion: "AV UNION 342"
      -codLocal: "0000"
      -ruc: "20609678047"
    }
    -partida: 
Greenter\Model\Despatch
\
Direction
 {#1768
      -ubigueo: "010101"
      -direccion: "AV HUSARES 111"
      -codLocal: "0000"
      -ruc: "20609678047"
    }
  }
  -docBaja: null
  -relDoc: null
  -addDocs: null
  -details: array:2 [
    0 => 
Greenter\Model\Despatch
\
DespatchDetail
 {#1780
      -codigo: "CEPILLO COLGATE PREM CLEAN MED"
      -descripcion: "CEPILLO COLGATE PREM CLEAN MED"
      -unidad: "UNIDAD"
      -cantidad: 12.0
      -codProdSunat: null
      -atributos: null
    }
    1 => 
Greenter\Model\Despatch
\
DespatchDetail
 {#1779
      -codigo: "GALL RELLENITAS CHOCOL. 36GR"
      -descripcion: "GALL RELLENITAS CHOCOL. 36GR"
      -unidad: "UNIDAD"
      -cantidad: 21.0
      -codProdSunat: null
      -atributos: null
    }
  ]
}
*/ 

/*
========== API SEND ===========
====== DEVUELVE ======
Greenter\Model\Response\SummaryResult {#3846 // app\Http\Services\Facturacion\GuiaFacturacionService.php:97
  #success: false
  #error: 
  Greenter\Model\Response\Error
    {#2827
      #code: "500"
      #message: "Error inesperado"
    }
    #ticket: null
  }
            
  => 
  Greenter\Model\Response\SummaryResult {#3846 // app\Http\Services\Facturacion\GuiaFacturacionService.php:109
  #success: true
  #error: null
  #ticket: "test-deaa8866-7f39-451c-ab7b-65da610c07d9"
}
*/ 


/*
========= CONSULTAR SUNAT ========
=>RECIBE (TICKET)
=>DEVUELVE:

-CASO ERROR 
Greenter\Model\Response\StatusResult {#1801 // app\Http\Services\Facturacion\GuiaFacturacion\GuiaFacturacionService.php:232
  #success: false
  #error: 
Greenter\Model\Response\Error
{#1798
    #code: "2760"
    #message: ": Valor no se encuentra en el catalogo: 06 (nodo: "cbc:ID/schemeID" valor: "06")"
  }
  #cdrZip: null
  #cdrResponse: null
  #code: "99"
}

-CASO ACEPTADO 
Greenter\Model\Response\StatusResult {#1801 // app\Http\Services\Facturacion\GuiaFacturacion\GuiaFacturacionService.php:232
  #success: true
  #error: null
  #cdrZip: b"PK\x03\x04\x14\x00\x00\x00\x08\x00│å¼Zó╚¥─═\x03\x00\x00ï\x00\x00\e\x00\x00\x00R-20609678047-09-TM01-1.xmlØW[s┌<\x10}¤»­8¤ãùp\t\x1ECå@:├┤═t\x08i¹*õ\x05▄▒%WÆ\x13‗²·O‗┘ÿñä'{¸Þý¯┘§j\x08¯\x0EIl╝\x00Ò\x11%\x13ËÝ9ª\x01\x04Ë0"╗ë¨╝■b¦ÜwË½\x001\x7FûªqäæÉ└\x15­ö\x12\x0Eå<L©ÅÏ─╠\x18±)Ô\x11¸\tJÇ¹<\x05\x1CmK╝ƒmbƒÒ=$╚?­░ï╩‗╠Æ\x0EÔB║9M\x12J\x1E\x0E\x02ê*C¥JJ é\x1FI±\x06\x7Fè¶^┬q'!·\x1CßlÀc░C\x02║HC>1¸Bñ¥m┐¥¥÷^ozöÝl¤q\x1C█\x19█\x12\x13‗hw]í9Eiì/\x02±×t){~P=Ï@^ ª)Ïµ¶╩0\x02®« | ¡\x16ï+kç¢0ù\x0EMZ"ƒDÕö¯ºhGÉ╚X9\v ö ±xI\x00ßÆl®nûÄ9"öH-ÒÞ┐\¤´ ÷44f±Ä▓Hýô3í\█uT(\v\x0EÏ┬nƒ\ Æh%¥Ê┌┤[QÛ³ ÖÌÚWòX\tep═8▓°\x1Eyâß\t¨Â└õþ\x04ã¾j91═ª[\x02Í\f\x11¥Ñ,ßmù¯³0®å╝U├CïWÁÁ\x13╗É■2I%Á}¥«`\x11ÝÇï\vıû\x1A^wk\3■Dq\x06Ý\x06Ïu\x07\x1A3gw\x0F¦q\x18N╔é»­Í1ñ┐\x07╬xü\x04:IJ9µ└D▒\x0E`zÉ´°°\x1EÏm@\x17┴Æ¾\fÏ\x13░\x08┼\x1D\x1D<B\x1EÕ·╔C­l¾\x07░PÙ¿\x08í¨╗\t÷Ã,┘\x00øÄı»8Ï░ƒ4°²ý\x7F[ù└>Ð░ÞD.y¢pý¾\eº­ÁÎÈ®5ƒ║@¯|e²Y\n╦┼ÈÙ9ü}b¡á¾î\vÜö╦F┌¦¦vT\x07õÒÓv\x13Ìä\x08,\f#ÎÛ{█¡5FÄcß\x10o\x07Ìµv3\x1E÷\v\x12²£Êmíz¯9Ì└r\x06ûÙòá┌Ë└«#┘=wÞ\x0Fn4Xn¼`ı-┌╔┌pÂO┤╣\e÷\x1Cî░▀jRU¢2?>▀?|Ö═ÎzòìSö¢²@L╝ı²U«▄▓\fecÙÙR\eèÆ╔såÄ3\x1C\x0FF#À╔«Z■\x01╦1Js¶s¯▄T%n<═v\x1D¿±\x1F═à▒½¬<Ü\Cæ@q¡ıL\x08ä¸ëv]µ05ñîá©c3\x15\x13╗Zv\x08½¼ì▄╬ð\x14╬¸SÐÙ({Z╚BÕ|╠\x17+#\x04#e\x19lP)\f\x15┌(\x00\tüÁk\x7FÀ\x13\x1Ft¾¦^û╣6âµ!WÇ!z¨t*ÒßÞÍÚÅ.NÕ$lÐ{è3%o§§h¨4M§ÎWvMF^\x7Fw\╦¡¥¥ú¢ë/Hµ4ä®Ë³Rsø\x0E^\x00Ã,J¾╝g¾ç\x1FÙ┘ó\x1Cq¦ú\x15▄æ§▒áÍöV\x1AÛYÀÆ¡╬ÛÐÈÝ╬Õ§×▒Ï\x12‗Â¥█#¥ ╦&Û╣╠¡Òö>Ê¦\tUú\x10Ñæt~rÐ\f¡│\x13±ß¬®ºóòA;ÙJß└¯■+5¢·\x1FPK\x01\x024\x03\x14\x00\x00\x00\x08\x00│å¼Zó╚¥─═\x03\x00\x00ï\x00\x00\e\x00\x00\x00\x00\x00\x00\x00\x01\x00\x00\x00ñü\x00\x00\x00\x00R-20609678047-09-TM01-1.xmlPK\x05\x06\x00\x00\x00\x00\x01\x00\x01\x00I\x00\x00\x00\x06\x04\x00\x00\x00\x00"
  #cdrResponse: 
    Greenter\Model\Response\CdrResponse
    {#1788
      #id: "TM01-1"
      #code: "0"
      #description: "ACEPTADA"
      #notes: array:1 [
        0 => "CDR de prueba"
      ]
      #reference: "https://url-test?hashqr=test"
    }
  #code: "0"
}
*/ 




