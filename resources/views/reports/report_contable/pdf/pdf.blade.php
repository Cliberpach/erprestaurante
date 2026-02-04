<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Contable</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            height: 100%;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 30px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .details p,
        .totals p {
            margin: 5px 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td {
            padding: 6px;
            vertical-align: top;
        }

        .header-table td {
            border: none;
        }

        .info-table-custom {
            margin-top: 20px;
            width: 100%;
        }

        .info-table-custom td {
            font-size: 12px;
            border: 1px solid #d4f1ff;
        }

        .info-table-custom .label {
            font-weight: bold;
            background-color: #f5f5f5;
        }



        .tbl-report-sale {
            margin-top: 20px;
            width: 100%;
            border: 1px solid #ccc;
        }

        .tbl-report-sale th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: left;
            padding: 6px;
            border: 1px solid #ccc;
            font-size: 12px;
        }

        .tbl-report-sale td {
            padding: 6px;
            border: 1px solid #ccc;
            font-size: 12px;
        }


        @page {
            margin-bottom: 40px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;

            height: 50px;
            text-align: center;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #ccc;
            background-color: #f8f9fa;
            padding-top: 4px;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Encabezado con logo e información de la empresa -->
        <table class="header-table">
            <tr>
                <!-- Columna 1: Imagen -->
                <td style="width: 20%; text-align: left;">
                    <img src="{{ $company->logo_url }}" alt="Logo"
                        style="height: 100px; object-fit: contain; max-width: 120px;">
                </td>

                <!-- Columna 2: Información de la empresa -->
                <td style="width: 60%; text-align: left;">
                    <h2 style="margin: 0; font-size: 14px; color: #3a6ea5;">{{ $company->business_name }}</h2>
                    <p style="margin: 0; font-size: 14px; color: #555;">RUC: {{ $company->ruc }}</p>
                    <p style="margin: 0; font-size: 14px; color: #555;">{{ $company->fiscal_address }}</p>
                    <p style="margin: 0; font-size: 14px; color: #555;">Teléfono: {{ $company->phone }}</p>
                    <p style="margin: 0; font-size: 14px; color: #555;">EMAIL: {{ $company->email }}</p>
                </td>

                <!-- Columna 3: Vacía -->
                <td style="width: 20%;"></td>
            </tr>
        </table>

        <div style="text-align: right; font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 10px;">
            REPORTE CONTABLE
        </div>

        <!-- Segunda tabla: Información adicional -->
        <table class="info-table-custom">
            <tr>
                <td class="label">USUARIO IMPRESIÓN:</td>
                <td>{{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <td class="label">FECHA IMPRESIÓN:</td>
                <td>{{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td class="label">FECHA INICIO:</td>
                <td>{{ $filters->get('start_date') }}</td>
            </tr>
            <tr>
                <td class="label">FECHA FIN:</td>
                <td>{{ $filters->get('end_date') }}</td>
            </tr>
        </table>

        <!-- Tercera tabla: Reporte de Ventas -->
        <table class="tbl-report-sale">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>TIPO</th>
                    <th>CÓDIGO</th>
                    <th>SERIE</th>
                    <th>CORRELATIVO</th>
                    <th>USER</th>
                    <th>CLIENTE TIPO DOC</th>
                    <th>CL COD DOC</th>
                    <th>CL DOC</th>
                    <th>CLIENTE</th>
                    <th>SUBTOTAL</th>
                    <th>IGV</th>
                    <th>%IGV</th>
                    <th>TOTAL</th>
                    <th>SUNAT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report_contable as $sale)
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
        <div class="footer" style="vertical-align:bottom;">
            <p>&copy; {{ now()->year }} {{ $company->business_name }} - Todos los derechos reservados</p>
        </div>
    </div>
</body>

</html>
