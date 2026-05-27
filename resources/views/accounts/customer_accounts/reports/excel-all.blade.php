<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Cuentas Cliente</title>
</head>
<body>
    <div>
        <table>
            <tr>
                <td style="width:200px; font-weight:bold;">EMPRESA</td>
                <td>{{ $company->abbreviated_business_name }}</td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">RUC</td>
                <td>{{ $company->ruc }}</td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">DIRECCIÓN</td>
                <td>{{ $company->fiscal_address }}</td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">TELÉFONO</td>
                <td>{{ $company->phone }}</td>
            </tr>
        </table>

        <br>

        <table>
            <tr>
                <td style="width:200px; font-weight:bold;">USUARIO IMPRESIÓN:</td>
                <td>{{ Auth::user()->name }}</td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">FECHA IMPRESIÓN:</td>
                <td>{{ now()->format('Y-m-d H:i:s') }}</td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">CLIENTE:</td>
                <td>{{ $filters->customer?->name ?? 'TODOS' }}</td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">ESTADO:</td>
                <td>{{ $filters->status ?: 'TODOS' }}</td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">FECHA INICIO:</td>
                <td>{{ $filters->start_date ?: '—' }}</td>
            </tr>
            <tr>
                <td style="width:200px; font-weight:bold;">FECHA FIN:</td>
                <td>{{ $filters->end_date ?: '—' }}</td>
            </tr>
        </table>

        <br>

        <table>
            <thead>
                <tr>
                    <th style="background:#1B587C;color:#fff;border:1px solid #4EA4D8;text-transform:uppercase;padding:4px 6px;">CLIENTE</th>
                    <th style="background:#1B587C;color:#fff;border:1px solid #4EA4D8;text-transform:uppercase;padding:4px 6px;">DOCUMENTO</th>
                    <th style="background:#1B587C;color:#fff;border:1px solid #4EA4D8;text-transform:uppercase;padding:4px 6px;">FECHA DOC</th>
                    <th style="background:#1B587C;color:#fff;border:1px solid #4EA4D8;text-transform:uppercase;padding:4px 6px;">MONTO</th>
                    <th style="background:#1B587C;color:#fff;border:1px solid #4EA4D8;text-transform:uppercase;padding:4px 6px;">PAGADO</th>
                    <th style="background:#1B587C;color:#fff;border:1px solid #4EA4D8;text-transform:uppercase;padding:4px 6px;">SALDO</th>
                    <th style="background:#1B587C;color:#fff;border:1px solid #4EA4D8;text-transform:uppercase;padding:4px 6px;">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>{{ $item->customer_info }}</td>
                    <td>{{ $item->document_serie }}</td>
                    <td>{{ $item->document_date }}</td>
                    <td>{{ $item->amount }}</td>
                    <td>{{ $item->paid }}</td>
                    <td>{{ $item->balance }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
