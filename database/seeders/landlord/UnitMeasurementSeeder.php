<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\GeneralTable\GeneralTable;
use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use Illuminate\Database\Seeder;

class UnitMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item_general                  =   new GeneralTable();
        $item_general->name            =   'UNIDADES DE MEDIDA';
        $item_general->description     =   'UNIDADES DE MEDIDA';
        $item_general->symbol          =   'UDM';
        $item_general->parameter       =   'UDM';
        $item_general->save();

        $unidades_medida = [
            ['descripcion' => 'BOBINAS', 'simbolo' => '4A','parameter'=>'4A'],
            ['descripcion' => 'AMPOLLETA', 'simbolo' => 'AMP','parameter'=>'AMP'],
            ['descripcion' => 'ARROBA', 'simbolo' => 'AR','parameter'=>'AR'],
            ['descripcion' => 'BARRA', 'simbolo' => 'BR','parameter'=>'BR'],
            ['descripcion' => 'BALDE', 'simbolo' => 'BJ','parameter'=>'BJ'],
            ['descripcion' => 'BARRILES', 'simbolo' => 'BLL','parameter'=>'BLL'],
            ['descripcion' => 'BOLSA', 'simbolo' => 'BG','parameter'=>'BG'],
            ['descripcion' => 'BOTELLAS', 'simbolo' => 'BO','parameter'=>'BO'],
            ['descripcion' => 'CAJA', 'simbolo' => 'BX','parameter'=>'BX'],
            ['descripcion' => 'CARTONES', 'simbolo' => 'CT','parameter'=>'CT'],
            ['descripcion' => 'CENTIMETRO CUADRADO', 'simbolo' => 'CMK','parameter'=>'CMK'],
            ['descripcion' => 'CENTIMETRO CUBICO', 'simbolo' => 'CMQ','parameter'=>'CMQ'],
            ['descripcion' => 'CENTIMETRO LINEAL', 'simbolo' => 'CMT','parameter'=>'CMT'],
            ['descripcion' => 'CIENTO DE UNIDADES', 'simbolo' => 'CEN','parameter'=>'CEN'],
            ['descripcion' => 'CILINDRO', 'simbolo' => 'CY','parameter'=>'CY'],
            ['descripcion' => 'CONOS', 'simbolo' => 'CJ','parameter'=>'CJ'],
            ['descripcion' => 'DOCENA', 'simbolo' => 'DZN','parameter'=>'DZN'],
            ['descripcion' => 'DOCENA POR 10**6', 'simbolo' => 'DZP','parameter'=>'DZP'],
            ['descripcion' => 'FARDO', 'simbolo' => 'BE','parameter'=>'BE'],
            ['descripcion' => 'GALON INGLES (4,545956L)', 'simbolo' => 'GLI','parameter'=>'GLI'],
            ['descripcion' => 'GRAMO', 'simbolo' => 'GRM','parameter'=>'GRM'],
            ['descripcion' => 'GRUESA', 'simbolo' => 'GRO','parameter'=>'GRO'],
            ['descripcion' => 'HECTOLITRO', 'simbolo' => 'HLT','parameter'=>'HLT'],
            ['descripcion' => 'HOJA', 'simbolo' => 'LEF','parameter'=>'LEF'],
            ['descripcion' => 'JUEGO', 'simbolo' => 'SET','parameter'=>'SET'],
            ['descripcion' => 'KILOGRAMO', 'simbolo' => 'KGM','parameter'=>'KGM'],
            ['descripcion' => 'KILOMETRO', 'simbolo' => 'KTM','parameter'=>'KTM'],
            ['descripcion' => 'KILOVATIO HORA', 'simbolo' => 'KWH','parameter'=>'KWH'],
            ['descripcion' => 'KIT', 'simbolo' => 'KIT','parameter'=>'KIT'],
            ['descripcion' => 'LATAS', 'simbolo' => 'CA','parameter'=>'CA'],
            ['descripcion' => 'LIBRAS', 'simbolo' => 'LBR','parameter'=>'LBR'],
            ['descripcion' => 'LITRO', 'simbolo' => 'LTR','parameter'=>'LTR'],
            ['descripcion' => 'MEGAWATT HORA', 'simbolo' => 'MWH','parameter'=>'MWH'],
            ['descripcion' => 'METRO', 'simbolo' => 'MTR','parameter'=>'MTR'],
            ['descripcion' => 'METRO CUADRADO', 'simbolo' => 'MTK','parameter'=>'MTK'],
            ['descripcion' => 'METRO CUBICO', 'simbolo' => 'MTQ','parameter'=>'MTQ'],
            ['descripcion' => 'MILIGRAMOS', 'simbolo' => 'MGM','parameter'=>'MGM'],
            ['descripcion' => 'MILILITRO', 'simbolo' => 'MLT','parameter'=>'MLT'],
            ['descripcion' => 'MILIMETRO', 'simbolo' => 'MMT','parameter'=>'MMT'],
            ['descripcion' => 'MILIMETRO CUADRADO', 'simbolo' => 'MMK','parameter'=>'MMK'],
            ['descripcion' => 'MILIMETRO CUBICO', 'simbolo' => 'MMQ','parameter'=>'MMQ'],
            ['descripcion' => 'MILLARES', 'simbolo' => 'MIL','parameter'=>'MIL'],
            ['descripcion' => 'MILLON DE UNIDADES', 'simbolo' => 'UM','parameter'=>'UM'],
            ['descripcion' => 'ONZAS', 'simbolo' => 'ONZ','parameter'=>'ONZ'],
            ['descripcion' => 'PALETAS', 'simbolo' => 'PF','parameter'=>'PF'],
            ['descripcion' => 'PAQUETE', 'simbolo' => 'PK','parameter'=>'PK'],
            ['descripcion' => 'PAR', 'simbolo' => 'PR','parameter'=>'PR'],
            ['descripcion' => 'PORCION', 'simbolo' => 'PT','parameter'=>'PT'],
            ['descripcion' => 'RESMA', 'simbolo' => 'RM','parameter'=>'RM'],
            ['descripcion' => 'ROLLO', 'simbolo' => 'RO','parameter'=>'RO'],
            ['descripcion' => 'SACO', 'simbolo' => 'SA','parameter'=>'SA'],
            ['descripcion' => 'SET', 'simbolo' => 'ST','parameter'=>'ST'],
            ['descripcion' => 'TAMBOR', 'simbolo' => 'TU','parameter'=>'TU'],
            ['descripcion' => 'TANQUE', 'simbolo' => 'TK','parameter'=>'TK'],
            ['descripcion' => 'TONELADA', 'simbolo' => 'TNE','parameter'=>'TNE'],
            ['descripcion' => 'TUBOS', 'simbolo' => 'TU','parameter'=>'TU'],
            ['descripcion' => 'UNIDAD', 'simbolo' => 'NIU','parameter'=>'NIU'],
            ['descripcion' => 'YARDA', 'simbolo' => 'YRD','parameter'=>'YRD'],
            ['descripcion' => 'GRAMO NETO', 'simbolo' => 'GRN','parameter'=>'GRN'],
            ['descripcion' => 'KILOGRAMO NETO', 'simbolo' => 'KGN','parameter'=>'KGN'],
            ['descripcion' => 'TONELADA LARGA', 'simbolo' => 'LTON','parameter'=>'LTON'],
            ['descripcion' => 'TONELADA CORTA', 'simbolo' => 'STON','parameter'=>'STON'],
            ['descripcion' => 'PIES CUBICOS', 'simbolo' => 'FTQ','parameter'=>'FTQ'],
            ['descripcion' => 'PIES CUADRADOS', 'simbolo' => 'FTK','parameter'=>'FTK'],
            ['descripcion' => 'PIE LINEAL', 'simbolo' => 'FT','parameter'=>'FT'],
            ['descripcion' => 'MIL', 'simbolo' => 'ML','parameter'=>'ML'],
            ['descripcion' => 'KILOMETRO POR HORA', 'simbolo' => 'KMH','parameter'=>'KMH'],
            ['descripcion' => 'MILLAS POR HORA', 'simbolo' => 'MPH','parameter'=>'MPH'],
            ['descripcion' => 'MILILITROS POR HORA', 'simbolo' => 'MLH','parameter'=>'MLH'],
            ['descripcion' => 'MILIGRAMOS POR HORA', 'simbolo' => 'MGH','parameter'=>'MGH'],
            ['descripcion' => 'MILIMETROS POR HORA', 'simbolo' => 'MMH','parameter'=>'MMH'],
            ['descripcion' => 'GRAMOS POR HORA', 'simbolo' => 'GRH','parameter'=>'GRH'],
            ['descripcion' => 'PIES POR HORA', 'simbolo' => 'FTH','parameter'=>'FTH'],
            ['descripcion' => 'CENTIMETROS POR HORA', 'simbolo' => 'CMH','parameter'=>'CMH'],
            ['descripcion' => 'METROS POR HORA', 'simbolo' => 'MTH','parameter'=>'MTH'],
            ['descripcion' => 'YARDAS POR HORA', 'simbolo' => 'YDH','parameter'=>'YDH'],
            ['descripcion' => 'MILILITROS POR MINUTO', 'simbolo' => 'MLM','parameter'=>'MLM'],
            ['descripcion' => 'MILIGRAMOS POR MINUTO', 'simbolo' => 'MGM','parameter'=>'MGM'],
            ['descripcion' => 'GRAMOS POR MINUTO', 'simbolo' => 'GRM','parameter'=>'GRM'],
            ['descripcion' => 'PIES POR MINUTO', 'simbolo' => 'FTM','parameter'=>'FTM'],
            ['descripcion' => 'CENTIMETROS POR MINUTO', 'simbolo' => 'CMM','parameter'=>'CMM'],
            ['descripcion' => 'METROS POR MINUTO', 'simbolo' => 'MTM','parameter'=>'MTM'],
            ['descripcion' => 'YARDAS POR MINUTO', 'simbolo' => 'YDM','parameter'=>'YDM'],
            ['descripcion' => 'MILILITROS POR SEGUNDO', 'simbolo' => 'MLS','parameter'=>'MLS'],
            ['descripcion' => 'MILIGRAMOS POR SEGUNDO', 'simbolo' => 'MGS','parameter'=>'MGS'],
            ['descripcion' => 'GRAMOS POR SEGUNDO', 'simbolo' => 'GRS','parameter'=>'GRS'],
            ['descripcion' => 'PIES POR SEGUNDO', 'simbolo' => 'FTS','parameter'=>'FTS'],
            ['descripcion' => 'CENTIMETROS POR SEGUNDO', 'simbolo' => 'CMS','parameter'=>'CMS'],
            ['descripcion' => 'METROS POR SEGUNDO', 'simbolo' => 'MTS','parameter'=>'MTS'],
            ['descripcion' => 'YARDAS POR SEGUNDO', 'simbolo' => 'YDS','parameter'=>'YDS'],
            ['descripcion' => 'TONELADA MÉTRICA', 'simbolo' => 'T','parameter'=>'T'],
            ['descripcion' => 'UNIDADES POR METRO', 'simbolo' => 'UPM','parameter'=>'UPM'],
            ['descripcion' => 'UNIDADES POR CENTÍMETRO', 'simbolo' => 'UPC','parameter'=>'UPC'],
            ['descripcion' => 'UNIDADES POR MILÍMETRO', 'simbolo' => 'UPMM','parameter'=>'UPMM'],
            ['descripcion' => 'UNIDADES POR GRAMO', 'simbolo' => 'UPG','parameter'=>'UPG'],
            ['descripcion' => 'UNIDADES POR KILOGRAMO', 'simbolo' => 'UPKG','parameter'=>'UPKG'],
            ['descripcion' => 'UNIDADES POR LITRO', 'simbolo' => 'UPL','parameter'=>'UPL']
        ];

        foreach ($unidades_medida as $unidad_medida) {
            $item                   = new GeneralTableDetail();
            $item->name             = $unidad_medida['descripcion'];
            $item->description      = $unidad_medida['descripcion'];
            $item->symbol           = $unidad_medida['simbolo'];
            $item->parameter        = $unidad_medida['parameter'];
            $item->general_table_id = $item_general->id;
            $item->save();
        }
    }
}
