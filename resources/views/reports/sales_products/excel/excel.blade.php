<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Venta Producto</title>
</head>

<body>
    <div>
        <table>
            <tr>
                <td style="width: 220px; font-weight: bold;">EMPRESA</td>
                <td style="font-size: 12px;">{{ $company->business_name }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">RUC</td>
                <td style="font-size: 10px;">{{ $company->ruc }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">DIRECCIÓN</td>
                <td style="font-size: 10px;">{{ $company->fiscal_address }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">TELÉFONO</td>
                <td style="font-size: 10px;">{{ $company->phone }}</td>
            </tr>
            <tr>
                <td style="width: 220px; font-weight: bold;">EMAIL</td>
                <td style="font-size: 10px;">{{ $company->email }}</td>
            </tr>
        </table>

        <div class="header-title">
            REPORTE VENTA PRODUCTO
        </div>

        <!-- Información adicional -->
        <table class="info-table">

            <tr>
                <td style="width:160px;"><strong>USUARIO IMPRESIÓN:</strong></td>
                <td>{{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <td style="width:160px;"><strong>FECHA IMPRESIÓN:</strong></td>
                <td>{{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>FECHA INICIO:</strong></td>
                <td>{{ $filters->get('start_date') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>FECHA FIN:</strong></td>
                <td>{{ $filters->get('end_date') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>PRODUCTO:</strong></td>
                <td>{{ $filters->get('product_name') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>CATEGORÍA:</strong></td>
                <td>{{ $filters->get('category_name') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>MARCA:</strong></td>
                <td>{{ $filters->get('brand_name') }}</td>
            </tr>
            <tr>
                <td style="background-color: yellow; font-weight: bold;" class="label"><strong>TOTAL UTILIDAD:</strong>
                </td>
                <td style="background-color: yellow; font-weight: bold;">
                    {{ number_format($filters->get('utility_total'), 2, '.', ',') }}
                </td>
            </tr>
        </table>

        <!-- Tabla del reporte -->
        <table>
            <thead>
                <tr>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        CANT
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        PRODUCTO
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        PRECIO VENTA
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        PRECIO COMPRA
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        IMPORTE VENTA
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        IMPORTE COMPRA
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        UTILIDAD
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->sale_price }}</td>
                        <td>{{ $item->purchase_price }}</td>
                        <td>{{ $item->total }}</td>
                        <td>{{ $item->purchase_total }}</td>
                        <td>{{ $item->utility }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ now()->year }} {{ $company->razon_social }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>

</html>
