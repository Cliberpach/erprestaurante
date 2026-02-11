<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $credit_note->serie . '-' . $credit_note->correlative }}</title>
    <style>
        /* ========== VARIABLES DE COLOR COMANDAPRO ========== */
        /*
        Celeste Primary: #3B82F6
        Celeste Light: #60A5FA
        Celeste Muy Claro: #E0F2FE
        Azul Oscuro: #1E3A8A
        Azul Medio: #2563EB
        Blanco: #FFFFFF
        */

        /* ========== ESTILOS GENERALES ========== */
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            height: 100%;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
            background-color: #ffffff;
        }

        .container {
            width: 100%;
            margin: 20px auto;
            background-color: #ffffff;
        }

        /* ========== TABLA HEADER ========== */
        .header-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .header-table td {
            border: none;
            padding: 8px;
        }

        .company-name {
            margin: 0;
            font-size: 16px;
            color: #1E3A8A;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .company-info {
            margin: 2px 0;
            font-size: 11px;
            color: #333;
        }

        .company-info strong {
            color: #2563EB;
        }

        /* ========== CUADRO NOTA DE CRÉDITO ========== */
        .credit-note-box {
            border: 2px solid #3B82F6;
            background-color: #E0F2FE;
            padding: 12px;
            font-size: 12px;
            margin-bottom: 10px;
            text-align: center;
            border-radius: 5px;
        }

        .credit-note-box strong {
            display: block;
            color: #1E3A8A;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .credit-note-number {
            font-size: 16px;
            font-weight: bold;
            color: #2563EB;
            letter-spacing: 1px;
        }

        /* ========== TABLA DE INFORMACIÓN ========== */
        .info-table {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
            background-color: #ffffff;
            border: 1px solid #60A5FA;
        }

        .info-table td {
            font-size: 10px;
            padding: 6px 10px;
            border: 1px solid #BAE6FD;
        }

        .info-table .label-cell {
            font-weight: bold;
            background-color: #E0F2FE;
            color: #1E3A8A;
            width: 25%;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.3px;
        }

        .info-table .value-cell {
            background-color: #ffffff;
            color: #333;
        }

        /* ========== TABLA DE DETALLES (ITEMS) ========== */
        .details-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #ffffff;
            border: 2px solid #3B82F6;
        }

        .details-table thead {
            background-color: #3B82F6;
        }

        .details-table th {
            font-weight: bold;
            text-align: center;
            padding: 8px 6px;
            border: 1px solid #2563EB;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
            background-color: #3B82F6;
        }

        .details-table tbody tr {
            background-color: #ffffff;
        }

        .details-table tbody tr:nth-child(even) {
            background-color: #F0F9FF;
        }

        .details-table tbody tr:hover {
            background-color: #E0F2FE;
        }

        .details-table td {
            padding: 6px 8px;
            border: 1px solid #BAE6FD;
            font-size: 10px;
            color: #1a1a1a;
        }

        .details-table td:first-child {
            text-align: center;
            font-weight: bold;
            color: #1E3A8A;
        }

        .details-table td:nth-child(3),
        .details-table td:nth-child(4) {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #1a1a1a;
        }

        /* ========== TABLA DE TOTALES ========== */
        .totals-container {
            width: 100%;
            margin-top: 15px;
        }

        .totals-table {
            width: 100%;
            font-size: 11px;
            border-collapse: collapse;
            background-color: #ffffff;
            border: 1px solid #60A5FA;
        }

        .totals-table tr {
            border-bottom: 1px solid #E0F2FE;
        }

        .totals-table td {
            padding: 6px 12px;
        }

        .totals-table .label-total {
            text-align: right;
            color: #1E3A8A;
            font-weight: 600;
            background-color: #F0F9FF;
        }

        .totals-table .value-total {
            text-align: right;
            font-family: 'Courier New', monospace;
            color: #1a1a1a;
            background-color: #ffffff;
            min-width: 100px;
            font-weight: bold;
        }

        .totals-table .row-total-final .label-total {
            background-color: #3B82F6;
            color: #ffffff;
            font-size: 12px;
            font-weight: bold;
        }

        .totals-table .row-total-final .value-total {
            background-color: #E0F2FE;
            color: #1E3A8A;
            font-size: 13px;
            font-weight: bold;
        }

        /* ========== LEYENDA ========== */
        .legend-box {
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #F0F9FF;
            border-left: 4px solid #3B82F6;
            font-size: 10px;
            color: #333;
            border-radius: 3px;
        }

        .legend-box strong {
            color: #1E3A8A;
            font-size: 11px;
        }

        /* ========== FOOTER ========== */
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
            font-size: 10px;
            color: #60A5FA;
        }

        .footer-content {
            border-top: 2px solid #3B82F6;
            padding-top: 10px;
            background-color: #F0F9FF;
        }

        .footer-content p {
            margin: 5px 0;
            color: #1E3A8A;
        }

        /* ========== QR CODE ========== */
        .qr-container {
            text-align: right;
            padding: 0;
            margin-right: -10px;
            border: 2px solid #E0F2FE;
            padding: 5px;
            border-radius: 5px;
            background-color: #ffffff;
        }

        /* ========== LOGO CONTAINER ========== */
        .logo-container {
            text-align: left;
            vertical-align: top;
            padding: 10px;
        }

        .logo-img {
            height: 100px;
            object-fit: contain;
            max-width: 120px;
            border: 2px solid #E0F2FE;
            padding: 5px;
            background-color: #ffffff;
            border-radius: 5px;
        }

        /* ========== UTILIDADES ========== */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-primary {
            color: #3B82F6;
        }

        .text-secondary {
            color: #1E3A8A;
        }

        /* ========== SEPARADOR ========== */
        .divider {
            height: 2px;
            background-color: #3B82F6;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- ========== ENCABEZADO ========== -->
        <table class="header-table" cellspacing="0" cellpadding="0">
            <tr>
                <!-- Logo -->
                <td class="logo-container" style="width: 20%;">
                    @if ($company->logo_url)
                        <img src="{{ public_path($company->logo_url) }}" alt="Logo" class="logo-img">
                    @else
                        <img src="{{ public_path('assets/images/tu_logo.jpg') }}" alt="Logo" class="logo-img">
                    @endif
                </td>

                <!-- Información de la empresa -->
                <td style="width: 55%; text-align: left; vertical-align: top; padding-left: 15px;">
                    <h2 class="company-name">{{ $company->abbreviated_business_name  }}</h2>
                    <p class="company-info"><strong>RUC:</strong> {{ $company->ruc }}</p>
                    <p class="company-info"><strong>Dirección:</strong> {{ $company->fiscal_address }}</p>
                    <p class="company-info"><strong>Teléfono:</strong> {{ $company->phone }}</p>
                    <p class="company-info"><strong>Email:</strong> {{ $company->email }}</p>
                </td>

                <!-- Nota de crédito + QR -->
                <td style="width: 25%; vertical-align: top;">
                    <!-- Cuadro Nota de Crédito -->
                    <div class="credit-note-box">
                        <strong>NOTA DE CRÉDITO ELECTRÓNICA</strong>
                        <span class="credit-note-number">
                            {{ $credit_note->serie }}-{{ $credit_note->correlative }}
                        </span>
                    </div>

                    <!-- QR -->
                    @if ($credit_note->ruta_qr)
                        <div class="qr-container">
                            <img src="{{ public_path($credit_note->ruta_qr) }}"
                                style="height: 100px; object-fit: contain;">
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- ========== INFORMACIÓN ADICIONAL ========== -->
        <table class="info-table">
            <tr>
                <td class="label-cell">Serie:</td>
                <td class="value-cell">{{ $credit_note->serie . '-' . $credit_note->correlative }}</td>
                <td class="label-cell">Fecha Emisión:</td>
                <td class="value-cell">{{ now()->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="label-cell">Motivo:</td>
                <td class="value-cell" colspan="3">{{ $credit_note->description_motive }}</td>
            </tr>
            <tr>
                <td class="label-cell">Doc. Afectado:</td>
                <td class="value-cell">{{ $credit_note->num_doc_affected }}</td>
                <td class="label-cell">Total:</td>
                <td class="value-cell font-bold text-primary">S/ {{ number_format($credit_note->total, 2) }}</td>
            </tr>
            <tr>
                <td class="label-cell">Cliente:</td>
                <td class="value-cell" colspan="3">{{ $credit_note->customer_name }}</td>
            </tr>
            <tr>
                <td class="label-cell">{{ $credit_note->customer_type_document }}:</td>
                <td class="value-cell">{{ $credit_note->customer_document_number }}</td>
                <td class="label-cell">Fecha Impresión:</td>
                <td class="value-cell">{{ now()->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>

        <!-- ========== TABLA DE DETALLES ========== -->
        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 10%;">CANT.</th>
                    <th style="width: 50%;">DESCRIPCIÓN</th>
                    <th style="width: 20%;">P. UNITARIO</th>
                    <th style="width: 20%;">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $item)
                    <tr>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>S/ {{ number_format($item->sale_price, 2) }}</td>
                        <td>S/ {{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ========== TOTALES ========== -->
        <table class="totals-container" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 65%;"></td>
                <td style="width: 35%;">
                    <table class="totals-table" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="label-total">Subtotal:</td>
                            <td class="value-total">S/ {{ number_format($credit_note->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label-total">
                                IGV ({{ number_format($credit_note->igv_percentage, 0) }}%):
                            </td>
                            <td class="value-total">S/ {{ number_format($credit_note->igv_amount, 2) }}</td>
                        </tr>
                        <tr class="row-total-final">
                            <td class="label-total">TOTAL:</td>
                            <td class="value-total">S/ {{ number_format($credit_note->total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- ========== LEYENDA ========== -->
        @if ($credit_note->legend)
            <div class="legend-box">
                <strong>SON:</strong> {{ $credit_note->legend }}
            </div>
        @endif

        <!-- ========== FOOTER ========== -->
        <footer class="footer-pdf">
            <div class="footer-content">
                <p><strong>{{ $company->abbreviated_business_name }}</strong></p>
                <p>&copy; {{ now()->year }} Todos los derechos reservados | ComandaPro</p>
            </div>
        </footer>

    </div>
</body>

</html>
