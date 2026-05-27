<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Cuentas Cliente</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; color: #333; }
        .container { width: 100%; margin: 0 auto; background: #fff; }

        .header-table td { border: none; padding: 4px; }

        .info-table { margin-top: 16px; width: 100%; border-collapse: collapse; }
        .info-table td { font-size: 10px; border: 1px solid #d4f1ff; padding: 4px 6px; }
        .info-table .label { font-weight: bold; background: #f5f5f5; width: 160px; }

        .tbl-report { margin-top: 16px; width: 100%; border-collapse: collapse; }
        .tbl-report th {
            background: #1B587C; color: #fff; text-align: center;
            border: 1px solid #4EA4D8; font-size: 9px; padding: 5px 4px;
            text-transform: uppercase;
        }
        .tbl-report td { padding: 5px 4px; border: 1px solid #ccc; font-size: 9px; }
        .tbl-report tr:nth-child(even) td { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        @page { margin: 30px 50px 70px 50px; size: A4 landscape; }
        footer {
            position: fixed; bottom: -50px; left: 0; right: 0;
            height: 40px; text-align: center; font-size: 9px; color: #777;
            border-top: 1px solid #ccc; padding-top: 6px;
        }
    </style>
</head>
<body>
    <div class="container">

        <table class="header-table">
            <tr>
                <td style="width:18%; vertical-align:top;">
                    @if($company->logo_url)
                        <img src="{{ public_path($company->logo_url) }}" alt="Logo"
                             style="height:80px; object-fit:contain; max-width:110px;">
                    @endif
                </td>
                <td style="vertical-align:top;">
                    <h2 style="margin:0; font-size:13px; color:#3a6ea5;">{{ $company->abbreviated_business_name }}</h2>
                    <p style="margin:2px 0; font-size:12px; color:#555;">RUC: {{ $company->ruc }}</p>
                    <p style="margin:2px 0; font-size:12px; color:#555;">{{ $company->fiscal_address }}</p>
                    <p style="margin:2px 0; font-size:12px; color:#555;">Teléfono: {{ $company->phone }}</p>
                </td>
                <td style="text-align:right; vertical-align:top;">
                    <span style="font-size:13px; font-weight:bold; color:#1B587C;">CUENTAS CLIENTE</span>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td class="label">CLIENTE:</td>
                <td>{{ $filters->customer?->name ?? 'TODOS' }}</td>
                <td class="label">ESTADO:</td>
                <td>{{ $filters->status ?: 'TODOS' }}</td>
            </tr>
            <tr>
                <td class="label">FECHA INICIO:</td>
                <td>{{ $filters->start_date ?: '—' }}</td>
                <td class="label">FECHA FIN:</td>
                <td>{{ $filters->end_date ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">USUARIO IMPRESIÓN:</td>
                <td>{{ Auth::user()->name }}</td>
                <td class="label">FECHA IMPRESIÓN:</td>
                <td>{{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>

        <table class="tbl-report">
            <thead>
                <tr>
                    <th>CLIENTE</th>
                    <th>DOCUMENTO</th>
                    <th>FECHA DOC</th>
                    <th>MONTO</th>
                    <th>PAGADO</th>
                    <th>SALDO</th>
                    <th>ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->customer_info }}</td>
                    <td class="text-center">{{ $item->document_serie }}</td>
                    <td class="text-center">{{ $item->document_date }}</td>
                    <td class="text-right">{{ number_format($item->amount, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($item->paid, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($item->balance, 2, '.', ',') }}</td>
                    <td class="text-center">{{ $item->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <footer>
            <p>&copy; {{ now()->year }} {{ $company->abbreviated_business_name }} — Todos los derechos reservados</p>
        </footer>
    </div>
</body>
</html>
