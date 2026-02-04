<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Contable</title>
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
            REPORTE CONTABLE
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
                <td>{{ $filters->get('date_start') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>FECHA FIN:</strong></td>
                <td>{{ $filters->get('date_end') }}</td>
            </tr>
            {{-- <tr>
                <td style="background-color: yellow; font-weight: bold;" class="label"><strong>TOTAL:</strong>
                </td>
                <td style="background-color: yellow; font-weight: bold;">
                    {{ number_format($filters->get('total'), 2, '.', ',') }}
                </td>
            </tr> --}}
        </table>

        <!-- Tabla del reporte -->
        <table>
            <thead>
                <tr>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        FECHA COMPROBANTE
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        TIPO COMPROBANTE
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        CÓDIGO
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        SERIE
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        CORRELATIVO
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        REGISTRADOR
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        CLIENTE TIPO DOC
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        CLIENTE COD DOC
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        CLIENTE NRO DOC
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        CLIENTE NOMBRE
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        SUBTOTAL
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        IGV
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        %IGV
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        TOTAL
                    </th>

                    <th width="30"
                        style="background:#1B587C;color:white;text-align: center;border:1px solid #4EA4D8;text-transform: uppercase">
                        SUNAT
                    </th>

                </tr>
            </thead>
            <tbody>
                @foreach ($data as $sale)
                    <tr>
                        <td>{{ $sale->created_at }}</td>
                        <td>{{ $sale->type_sale_name }}</td>
                        <td>{{ $sale->type_sale_code }}</td>
                        <td>{{ $sale->serie }}</td>
                        <td>{{ $sale->correlative }}</td>
                        <td>{{ $sale->creator_user_name }}</td>
                        <td>{{ $sale->customer_type_document }}</td>
                        <td>{{ $sale->customer_document_code }}</td>
                        <td>{{ $sale->customer_document_number }}</td>
                        <td>{{ $sale->customer_name }}</td>

                        <td>{{ number_format($sale->subtotal, 2, '.', ',') }}</td>
                        <td>{{ number_format($sale->igv_amount, 2, '.', ',') }}</td>
                        <td>{{ number_format($sale->igv_percentage, 2, '.', ',') }}</td>
                        <td>{{ number_format($sale->total, 2, '.', ',') }}</td>

                        <td>{{ $sale->sunat_status }}</td>
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
