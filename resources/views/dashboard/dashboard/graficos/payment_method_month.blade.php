<style>
    * {
        font-family:
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            Roboto,
            Helvetica,
            Arial,
            "Apple Color Emoji",
            "Segoe UI Emoji",
            "Segoe UI Symbol",
            sans-serif;
        background: var(--highcharts-background-color);
        color: var(--highcharts-neutral-color-100);
    }

    #container-payment-methods-month {
        height: 400px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid var(--highcharts-neutral-color-10, #e6e6e6);
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: var(--highcharts-neutral-color-60, #666);
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tbody tr:nth-child(even) {
        background: var(--highcharts-neutral-color-3, #f7f7f7);
    }

    .highcharts-description {
        margin: 0.3rem 10px;
    }
</style>


<div class="d-flex justify-content-end">
    <button class="btn btn-success" id="excel-payment_method_month"><i class="fas fa-file-excel"></i> Excel</button>
</div>

<figure class="highcharts-figure">
    <div id="container-payment-methods-month"></div>
    <p class="highcharts-description">

    </p>
    <div id="sliders-payment-methods-month">
        <table>
            <tr>
                <td><label for="alpha">Alpha Angle</label></td>
                <td><input id="alpha" type="range" min="0" max="45" value="15" /> <span
                        id="alpha-value-payment-methods-month" class="value"></span></td>
            </tr>
            <tr>
                <td><label for="beta">Beta Angle</label></td>
                <td><input id="beta" type="range" min="-45" max="45" value="15" /> <span
                        id="beta-value-payment-methods-month" class="value"></span></td>
            </tr>
            <tr>
                <td><label for="depth">Depth</label></td>
                <td><input id="depth" type="range" min="20" max="100" value="50" /> <span
                        id="depth-value-payment-methods-month" class="value"></span></td>
            </tr>
        </table>
    </div>
</figure>

<script>
    //===== FORMATO DE DATA ======
    /*
    data: [
                    ['Toyota', 1795],
                    ['Volkswagen', 1242],
                    ['Volvo', 1074],
                    ['Tesla', 832],
                    ['Hyundai', 593],
                    ['MG', 509],
                    ['Skoda', 471],
                    ['BMW', 442],
                    ['Ford', 385],
                    ['Nissan', 371]
        ],
    */
    function setPaymentMethodMonth(titulo, subtitulo, datos) {
        const chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container-payment-methods-month',
                type: 'column',
                options3d: {
                    enabled: true,
                    alpha: 15,
                    beta: 15,
                    depth: 50,
                    viewDistance: 25
                }
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    enabled: false
                }
            },
            tooltip: {
                headerFormat: '<b>{point.key}</b><br>',
                pointFormat: 'Monto: {point.y}'
            },
            title: {
                text: titulo
            },
            subtitle: {
                text: subtitulo
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                column: {
                    depth: 25,
                    dataLabels: {
                        enabled: true,
                        format: 'S/ {point.y:,.2f}'
                    }
                }
            },
            series: [{
                data: datos,
                colorByPoint: true
            }],
            lang: getLang()
        });

        document.querySelectorAll(
            '#sliders-payment-methods-month input'
        ).forEach(input => input.addEventListener('input', e => {
            chart.options.chart.options3d[e.target.id] = parseFloat(e.target.value);
            showValuesPaymentMethodsMonth();
            chart.redraw(false);
        }));

        removeCreditos();
    }

    function showValuesPaymentMethodsMonth() {
        document.getElementById(
            'alpha-value-payment-methods-month'
        ).innerHTML = chart.options.chart.options3d.alpha;
        document.getElementById(
            'beta-value-payment-methods-month'
        ).innerHTML = chart.options.chart.options3d.beta;
        document.getElementById(
            'depth-value-payment-methods-month'
        ).innerHTML = chart.options.chart.options3d.depth;
    }

    function eventsPaymentMonth() {
        document.querySelector('#excel-payment_method_month').addEventListener('click', (e) => {
            excelPaymentMonth();
        })
    }

    function excelPaymentMonth() {

        const url = @json(route('tenant.dashboard.dashboard.excelMetodosPagoMes'));

        const params = {
            year: document.querySelector('#filtro_anio').value,
            month: document.querySelector('#filtro_mes').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;

    }
</script>
