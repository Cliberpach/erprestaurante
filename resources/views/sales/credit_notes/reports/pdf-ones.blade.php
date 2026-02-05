<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $credit_note->serie . '-' . $credit_note->correlative }}</title>
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
            padding: 4px;
            vertical-align: top;
        }

        .header-table td {
            border: none;
        }

        .info-table-custom {
            margin-top: 6px;
            width: 100%;
        }

        .info-table-custom td {
            font-size: 10px;
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
            padding: 4px;
            border: 1px solid #ccc;
            font-size: 11px;
        }

        .tbl-report-sale td {
            padding: 4px;
            border: 1px solid #ccc;
            font-size: 11px;
        }


        /*======== FOOTER ==========*/
        @page {
            margin: 30px 50px 90px 50px;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 80px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            background-color: #f2f2f2;
        }

        .footer-content {
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Encabezado con logo e información de la empresa -->
        <table class="header-table" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <!-- Columna 1: Imagen -->
                <td style="width: 20%; text-align: left; vertical-align: top;">
                    @if ($company->logo_url)
                        <img src="{{ public_path($company->logo_url) }}" alt="Logo"
                            style="height: 100px; object-fit: contain; max-width: 120px;">
                    @else
                        <img src="{{ public_path('assets/images/tu_logo.jpg') }}" alt="Logo"
                            style="height: 100px; object-fit: contain; max-width: 120px;">
                    @endif
                </td>

                <!-- Columna 2: Información de la empresa -->
                <td style="width: 55%; text-align: left; vertical-align: top;">
                    <h2 style="margin: 0; font-size: 12px; color: #3a6ea5;">
                        {{ $company->razon_social }}
                    </h2>
                    <p style="margin: 0; font-size: 12px;">RUC: {{ $company->ruc }}</p>
                    <p style="margin: 0; font-size: 12px;">{{ $company->direccion }}</p>
                    <p style="margin: 0; font-size: 12px;">TELÉFONO: {{ $company->telefono }}</p>
                    <p style="margin: 0; font-size: 12px;">EMAIL: {{ $company->correo }}</p>
                </td>

                <!-- Columna 3: Nota de crédito + QR (derecha) -->
                <td style="width: 25%; vertical-align: top;">

                    <!-- Cuadro Nota de Crédito -->
                    <div
                        style="
                        border: 1px solid #000;
                        padding: 8px;
                        font-size: 12px;
                        margin-bottom: 8px;
                        text-align: center;
                    ">
                        <strong>NOTA DE CRÉDITO</strong><br>
                        <span style="font-size: 13px; font-weight: bold;">
                            {{ $credit_note->serie }}-{{ $credit_note->correlative }}
                        </span>
                    </div>

                    <!-- QR -->
                    @if ($credit_note->ruta_qr)
                        <div style="text-align: right;padding:0;margin-right:-10px;">
                            <img src="{{ public_path($credit_note->ruta_qr) }}"
                                style="height: 120px; object-fit: contain;">
                        </div>
                    @endif

                </td>

            </tr>
        </table>

        <!-- Segunda tabla: Información adicional -->
        <table class="info-table-custom">
            <tr>
                <td class="label">SERIE:</td>
                <td>{{ $credit_note->serie . '-' . $credit_note->correlative }}</td>
            </tr>
            <tr>
                <td class="label">MOTIVO:</td>
                <td>{{ $credit_note->description_motive }}</td>
            </tr>
            <tr>
                <td class="label">DOC AFECTADO:</td>
                <td>{{ $credit_note->num_doc_affected }}</td>
            </tr>
            <tr>
                <td class="label">TOTAL:</td>
                <td>{{ $credit_note->total }}</td>
            </tr>
            <tr>
                <td class="label">CLIENTE:</td>
                <td>{{ $credit_note->customer_name }}</td>
            </tr>
            <tr>
                <td class="label">{{ $credit_note->customer_type_document }}:</td>
                <td>{{ $credit_note->customer_document_number }}</td>
            </tr>
            <tr>
                <td class="label">FECHA IMPRESIÓN:</td>
                <td>{{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>

        <!-- Tercera tabla: Reporte Kardex -->
        <table class="tbl-report-sale">
            <thead>
                <tr>
                    <th>CANT</th>
                    <th>ITEM</th>
                    <th>P.VENTA</th>
                    <th>IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $item)
                    <tr>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ number_format($item->sale_price, 2) }}</td>
                        <td>{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ===== TOTALES ===== -->
        <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%;">
                    <table width="100%" cellspacing="0" cellpadding="4" style="font-size: 12px;">
                        <tr>
                            <td style="text-align: right;">SUBTOTAL:</td>
                            <td style="text-align: right;">
                                S/ {{ number_format($credit_note->subtotal, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;">
                                IGV ({{ number_format($credit_note->igv_percentage, 0) }}%):
                            </td>
                            <td style="text-align: right;">
                                S/ {{ number_format($credit_note->igv_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right; font-weight: bold;">
                                TOTAL:
                            </td>
                            <td style="text-align: right; font-weight: bold;">
                                S/ {{ number_format($credit_note->total, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- ===== LEYENDA ===== -->
        @if ($credit_note->legend)
            <p style="margin-top: 8px; font-size: 11px;">
                <strong>SON:</strong> {{ $credit_note->legend }}
            </p>
        @endif

        <!-- Footer -->
        <footer class="footer-pdf">
            <div class="footer-content">
                <p>&copy; {{ now()->year }} {{ $company->razon_social }} - Todos los derechos reservados</p>
            </div>
        </footer>

    </div>
</body>
</html>
