<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CUENTA CLIENTE</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #222;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 28px 45px 80px 45px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0; right: 0;
            height: 50px;
            text-align: center;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }

        /* ── Encabezado ── */
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: none; vertical-align: top; padding: 4px; }

        .box-title {
            border: 2px solid #2563eb;
            border-radius: 4px;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            color: #2563eb;
            line-height: 1.5;
        }
        .box-title span { font-size: 13px; color: #1e3a8a; }

        /* ── Info de la cuenta ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        .info-table td {
            font-size: 10px;
            padding: 4px 6px;
            border: 1px solid #d1d5db;
        }
        .info-table .lbl {
            font-weight: bold;
            background-color: #f3f4f6;
            width: 22%;
            white-space: nowrap;
        }
        .info-table .val { width: 28%; }

        /* ── Badge estado ── */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
        }
        .badge-pendiente { background: #dc2626; }
        .badge-pagado    { background: #16a34a; }
        .badge-anulado   { background: #374151; }

        /* ── Tabla detalle ── */
        .tbl-detalle {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .tbl-detalle th {
            background-color: #2563eb;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            padding: 5px 4px;
            border: 1px solid #1d4ed8;
        }
        .tbl-detalle td {
            font-size: 9px;
            padding: 4px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .tbl-detalle tbody tr:nth-child(even) td { background: #f9fafb; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* ── Totales ── */
        .totals-table {
            width: 260px;
            margin-left: auto;
            margin-top: 12px;
            border-collapse: collapse;
        }
        .totals-table td {
            font-size: 10px;
            padding: 3px 6px;
            border: 1px solid #e5e7eb;
        }
        .totals-table .lbl-total { font-weight: bold; background: #f3f4f6; text-align: right; }
        .totals-table .val-total { text-align: right; }
        .totals-table .row-saldo td { font-weight: bold; color: #dc2626; }
        .totals-table .row-pagado td { color: #16a34a; }
    </style>
</head>

<body>

    {{-- ── Encabezado ────────────────────────────────────────────────── --}}
    <table class="header-table">
        <tr>
            {{-- Logo --}}
            <td style="width:18%;">
                @if($company->logo_url)
                    <img src="{{ public_path($company->logo_url) }}" alt="Logo"
                         style="height:80px;object-fit:contain;max-width:110px;">
                @endif
            </td>

            {{-- Datos empresa --}}
            <td style="width:58%;">
                <p style="margin:0;font-size:13px;font-weight:bold;color:#1e3a8a;">
                    {{ strtoupper($company->abbreviated_business_name ?? $company->business_name) }}
                </p>
                <p style="margin:2px 0;font-size:10px;color:#555;">RUC: {{ $company->ruc }}</p>
                <p style="margin:2px 0;font-size:10px;color:#555;">{{ $company->fiscal_address }}</p>
                @if($company->phone)
                    <p style="margin:2px 0;font-size:10px;color:#555;">Tel: {{ $company->phone }}</p>
                @endif
                @if($company->email)
                    <p style="margin:2px 0;font-size:10px;color:#555;">{{ $company->email }}</p>
                @endif
            </td>

            {{-- Recuadro número de cuenta --}}
            <td style="width:24%; text-align:right;">
                <div class="box-title">
                    CUENTA CLIENTE<br>
                    <span>CC-{{ str_pad($cuenta->id, 8, '0', STR_PAD_LEFT) }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- ── Datos de la cuenta ─────────────────────────────────────────── --}}
    @php
        $badgeClass = match($cuenta->status) {
            'PAGADO'   => 'badge-pagado',
            'ANULADO'  => 'badge-anulado',
            default    => 'badge-pendiente',
        };
        $clienteLabel = $cliente
            ? ($cliente->type_document_abbreviation . ':' . $cliente->document_number . ' — ' . $cliente->name)
            : '—';
    @endphp

    <table class="info-table">
        <tr>
            <td class="lbl">FECHA IMPRESIÓN</td>
            <td class="val">{{ now()->format('d/m/Y H:i:s') }}</td>
            <td class="lbl">DOCUMENTO</td>
            <td class="val">{{ $documento }}</td>
        </tr>
        <tr>
            <td class="lbl">FECHA REGISTRO</td>
            <td class="val">{{ \Carbon\Carbon::parse($cuenta->document_date)->format('d/m/Y') }}</td>
            <td class="lbl">ESTADO</td>
            <td class="val">
                <span class="badge {{ $badgeClass }}">{{ $cuenta->status }}</span>
            </td>
        </tr>
        <tr>
            <td class="lbl">CLIENTE</td>
            <td class="val" colspan="3">{{ $clienteLabel }}</td>
        </tr>
        @if($cuenta->agreement)
        <tr>
            <td class="lbl">ACUERDO</td>
            <td class="val" colspan="3">{{ $cuenta->agreement }}</td>
        </tr>
        @endif
        @if($cuenta->instance_serie)
        <tr>
            <td class="lbl">PEDIDO</td>
            <td class="val" colspan="3">
                {{  $cuenta->instance_serie }}
            </td>
        </tr>
        @endif
    </table>

    {{-- ── Detalle de pagos ────────────────────────────────────────────── --}}
    @if($detalle->count() > 0)
        <table class="tbl-detalle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>FECHA</th>
                    <th>OBSERVACIÓN</th>
                    <th>EFECTIVO</th>
                    <th>IMPORTE</th>
                    <th>TOTAL PAGADO</th>
                    <th>SALDO RESTANTE</th>
                    <th>IMAGEN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalle as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center" style="white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                        </td>
                        <td>{{ $item->observation ?? '—' }}</td>
                        <td class="text-right">S/ {{ number_format($item->cash ?? 0, 2) }}</td>
                        <td class="text-right">S/ {{ number_format($item->amount ?? 0, 2) }}</td>
                        <td class="text-right" style="font-weight:bold;">
                            S/ {{ number_format($item->total ?? 0, 2) }}
                        </td>
                        <td class="text-right">S/ {{ number_format($item->balance ?? 0, 2) }}</td>
                        <td class="text-center">
                            @if($item->img_route)
                                <img src="{{ public_path($item->img_route) }}"
                                     style="height:55px;width:55px;object-fit:cover;border-radius:3px;">
                            @else
                                <span style="color:#999;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align:center;color:#999;margin-top:20px;font-size:10px;">
            Sin pagos registrados aún.
        </p>
    @endif

    {{-- ── Resumen de totales ──────────────────────────────────────────── --}}
    @php
        $totalPagado = $cuenta->amount - $cuenta->balance;
    @endphp
    <table class="totals-table">
        <tr>
            <td class="lbl-total">MONTO TOTAL</td>
            <td class="val-total">S/ {{ number_format($cuenta->amount, 2) }}</td>
        </tr>
        <tr class="row-pagado">
            <td class="lbl-total">PAGADO</td>
            <td class="val-total">S/ {{ number_format($totalPagado, 2) }}</td>
        </tr>
        <tr class="row-saldo">
            <td class="lbl-total">SALDO PENDIENTE</td>
            <td class="val-total">S/ {{ number_format($cuenta->balance, 2) }}</td>
        </tr>
    </table>

    {{-- ── Footer ──────────────────────────────────────────────────────── --}}
    <footer>
        {{ $company->abbreviated_business_name ?? '' }} &nbsp;|&nbsp;
        Generado el {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
        Página <span class="pagenum"></span>
    </footer>

</body>
</html>
