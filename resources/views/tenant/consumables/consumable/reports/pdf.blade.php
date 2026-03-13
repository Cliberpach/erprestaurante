<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Insumos</title>
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
            font-size: 12px;
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
            font-size: 11px;
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
            font-size: 11px;
        }

        .tbl-report-sale td {
            padding: 6px;
            border: 1px solid #ccc;
            font-size: 11px;
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
                    <h2 style="margin: 0; font-size: 14px; color: #3a6ea5;">{{ $company->abbreviated_business_name }}
                    </h2>
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
            LISTA DE INSUMOS
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
        </table>

        <!-- Tercera tabla: Reporte de Ventas -->
        <table class="tbl-report-sale">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NOMBRE</th>
                    <th>CATEGORÍA</th>
                    <th>MARCA</th>
                    <th>P. VENTA</th>
                    <th>P. COMPRA</th>
                    <th>STOCK</th>
                    <th>STOCK MIN</th>
                    <th>COD FÁBRICA</th>
                    <th>COD BARRAS</th>
                    <th>UNIDAD</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category_name }}</td>
                        <td>{{ $item->brand_name }}</td>
                        <td>{{ number_format($item->sale_price, 2, '.', ',') }}</td>
                        <td>{{ number_format($item->purchase_price, 2, '.', ',') }}</td>
                        <td>{{ $item->stock }}</td>
                        <td>{{ $item->stock_min }}</td>
                        <td>{{ $item->code_factory }}</td>
                        <td>{{ $item->code_bar }}</td>
                        <td>{{ $item->unit_symbol }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer" style="vertical-align:bottom;">
            <p>&copy; {{ now()->year }} {{ $company->abbreviated_business_name }} - Todos los derechos reservados
            </p>
        </div>
    </div>
</body>

</html>
