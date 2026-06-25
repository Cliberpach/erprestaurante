<style>
    #waiter_ranking_month {
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
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    #waiter_ranking_month {
        height: 400px;
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
    <button class="btn btn-success" id="excel-waiter-month"><i class="fas fa-file-excel"></i> Excel</button>
</div>

<figure class="highcharts-figure">
    <div id="waiter_ranking_month"></div>
    {{-- <p class="highcharts-description">
        Highcharts supports drawing pyramid charts in 3D as well as 2D. While
        the 2D version is typically easier to read, the 3D version is sometimes
        used for decorative effect.
    </p> --}}
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
    function setRankingWaiterMonth(titulo, subtitulo, datos) {

        const chart = Highcharts.chart('waiter_ranking_month', {
            chart: {
                type: 'bar'
            },
            title: {
                text: titulo
            },
            subtitle: {
                text: 'De pedidos facturados no anulados'
            },
            xAxis: {
                categories: datos.names,
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                title: {
                    text: '(soles)',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: ' soles'
            },
            plotOptions: {
                bar: {
                    borderRadius: '50%',
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: 80,
                floating: true,
                borderWidth: 1,
                backgroundColor: 'var(--highcharts-background-color, #ffffff)',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Ventas',
                data: datos.amounts
            }],
            lang: getLang()
        });

        removeCreditos();
    }

    function eventsWaiterRankingMonth() {
        document.querySelector('#excel-waiter-month').addEventListener('click', (e) => {
            excelRankingWaiterMonth();
        })
    }

    function excelRankingWaiterMonth() {

        const url = @json(route('tenant.dashboard.dashboard.excelMeserosMes'));

        const params = {
            year: document.querySelector('#filtro_anio').value,
            month: document.querySelector('#filtro_mes').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;

    }
</script>
