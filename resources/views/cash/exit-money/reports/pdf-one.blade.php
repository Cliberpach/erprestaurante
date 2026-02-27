<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PDF Egreso #{{ $exit_money->id }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #2d2d2d;
            margin: 0;
            padding: 30px;
            background-color: #ffffff;
        }

        /* ── ENCABEZADO ── */
        .header {
            border-top: 3px solid #2563eb;
            border-bottom: 1px solid #d0d9f0;
            padding: 14px 4px 12px;
            margin-bottom: 22px;
        }
        .header-top {
            width: 100%;
            border-collapse: collapse;
        }
        .header-top td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a3a6b;
            margin: 0 0 3px 0;
            letter-spacing: 0.3px;
        }
        .header-subtitle {
            font-size: 11px;
            color: #6c757d;
            margin: 0;
        }
        .header-badge {
            text-align: right;
            font-size: 11px;
            font-weight: 700;
            color: #2563eb;
            border: 1px solid #b8ccf5;
            border-radius: 20px;
            padding: 4px 14px;
            white-space: nowrap;
            background-color: #f0f5ff;
        }

        /* ── INFO BOX ── */
        .info-box {
            border: 1px solid #d0d9f0;
            border-left: 3px solid #2563eb;
            border-radius: 4px;
            padding: 14px 18px;
            margin-bottom: 22px;
            background-color: #fafbff;
        }
        .info-box-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            color: #7f8c8d;
            margin: 0 0 10px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e9f0;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 4px 6px 4px 0;
            vertical-align: top;
            border: none;
            font-size: 12px;
        }
        .info-label {
            font-weight: bold;
            color: #4a5568;
            width: 145px;
        }
        .info-value {
            color: #2d2d2d;
        }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a3a6b;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #b8ccf5;
        }

        /* ── TABLA DETALLES ── */
        table.detalles {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        table.detalles thead tr {
            background-color: #f0f5ff;
            color: #1a3a6b;
            border-top: 1px solid #b8ccf5;
            border-bottom: 2px solid #b8ccf5;
        }
        table.detalles thead th {
            padding: 9px 12px;
            font-size: 11px;
            text-align: left;
            font-weight: bold;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        table.detalles thead th:last-child {
            text-align: right;
        }
        table.detalles tbody tr {
            border-bottom: 1px solid #eaeef5;
        }
        table.detalles tbody tr:nth-child(even) {
            background-color: #f8faff;
        }
        table.detalles tbody td {
            padding: 8px 12px;
            font-size: 12px;
            color: #2d2d2d;
        }
        table.detalles tbody td:last-child {
            text-align: right;
        }
        table.detalles .total-row {
            background-color: #f0f5ff !important;
            border-top: 1px solid #b8ccf5;
        }
        table.detalles .total-row td {
            font-weight: bold;
            color: #1a3a6b;
            padding: 9px 12px;
        }
        table.detalles .total-row td:last-child {
            text-align: right;
        }

        /* ── SUMMARY ── */
        .summary-wrap {
            text-align: right;
            margin-top: 4px;
        }
        .summary-box {
            display: inline-block;
            border: 1px solid #b8ccf5;
            border-left: 3px solid #2563eb;
            border-radius: 4px;
            background-color: #f0f5ff;
            padding: 12px 22px;
            text-align: right;
        }
        .summary-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            display: block;
            margin-bottom: 2px;
        }
        .summary-amount {
            font-size: 22px;
            font-weight: bold;
            color: #1a3a6b;
            letter-spacing: 0.5px;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 32px;
            text-align: center;
            font-size: 10px;
            color: #b0b7c3;
            border-top: 1px solid #e5e9f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <table class="header-top">
            <tr>
                <td>
                    <p class="header-title">Comprobante de Egreso #{{ $exit_money->id }}</p>
                    <p class="header-subtitle">Fecha de emisión: {{ $exit_money->created_at }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFO GENERAL --}}
    <div class="info-box">
        <p class="info-box-title">Información General</p>
        <table class="info-grid">
            <tr>
                <td class="info-label">Empresa:</td>
                <td class="info-value">{{ $company->business_name }}</td>
                <td class="info-label">RUC:</td>
                <td class="info-value">{{ $company->ruc }}</td>
            </tr>
            <tr>
                <td class="info-label">Proveedor:</td>
                <td class="info-value">{{ $exit_money->supplier->name }}</td>
                <td class="info-label">Tipo de pago:</td>
                <td class="info-value">{{ $exit_money->payment_method_name }}</td>
            </tr>
            <tr>
                <td class="info-label">Centro de Costos:</td>
                <td class="info-value" colspan="3">{{ $exit_money->cost_center_name }}</td>
            </tr>
        </table>
    </div>

    {{-- TABLA DETALLES --}}
    <p class="section-title">Detalles del Egreso</p>
    <table class="detalles">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Descripción</th>
                <th style="width:120px;">Total (S/)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDetalles = 0; @endphp
            @foreach ($exit_money_detail as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->description }}</td>
                    <td>{{ number_format($detail->total, 2) }}</td>
                </tr>
                @php $totalDetalles += $detail->total; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="2">Total de detalles</td>
                <td>{{ number_format($totalDetalles, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- TOTAL FINAL --}}
    <div class="summary-wrap">
        <div class="summary-box">
            <span class="summary-label">Total del Egreso</span>
            <span class="summary-amount">S/ {{ number_format($exit_money->total, 2) }}</span>
        </div>
    </div>

    {{-- PIE --}}
    <div class="footer">
        Documento generado electrónicamente &mdash; {{ $company->business_name }}
    </div>

</body>
</html>
