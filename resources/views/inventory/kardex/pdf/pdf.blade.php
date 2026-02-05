<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KARDEX PRODUCTO</title>
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


        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 15px 0;
            background-color: #f8f9fa;
            color: #6c757d;
            font-size: 12px;
            border-top: 1px solid #e9ecef;
        }

        .footer p {
            margin: 0;
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
            KARDEX PRODUCTO
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
        </table>

        <!-- Tercera tabla: Reporte Kardex -->
        <table class="tbl-report-sale">
            <thead>
                <tr>
                    {{-- <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Id
                    </th> --}}
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Fecha
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Producto
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Categoría
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Marca
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Tipo
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Doc
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Stock
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Entrada
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Salida
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Saldo
                    </th>
                    <th
                        style="background:#1B587C;color:white;text-align:center;border:1px solid #4EA4D8;text-transform:uppercase">
                        Registrador
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        {{-- <td>{{ $item->product_id }}</td> --}}
                        <td>{{ $item->date }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->category_name }}</td>
                        <td>{{ $item->brand_name }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->document_serie }}</td>
                        <td>{{ number_format($item->previous_stock, 2) }}</td>
                        <td>{{ number_format($item->entrada, 2) }}</td>
                        <td>{{ number_format($item->salida, 2) }}</td>
                        <td>{{ number_format($item->later_stock, 2) }}</td>
                        <td>{{ $item->creator_user_name }}</td>
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
