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
    <button class="btn btn-success" id="excel-cost-center-month"><i class="fas fa-file-excel"></i> Excel</button>
</div>

<figure class="highcharts-figure">
    <div id="cost_center_month"></div>
    {{-- <p class="highcharts-description">
        Highcharts supports drawing pyramid charts in 3D as well as 2D. While
        the 2D version is typically easier to read, the 3D version is sometimes
        used for decorative effect.
    </p> --}}
</figure>

<script src="https://code.highcharts.com/modules/cylinder.js"></script>
<script src="https://code.highcharts.com/modules/funnel3d.js"></script>
<script src="https://code.highcharts.com/modules/pyramid3d.js"></script>
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
    function setCostCenterMonth(titulo, subtitulo, datos) {

        const chart = Highcharts.chart('cost_center_month', {
            chart: {
                type: 'pyramid3d',
                options3d: {
                    enabled: true,
                    alpha: 10,
                    depth: 50,
                    viewDistance: 50
                }
            },
            title: {
                text: titulo
            },
            lang: getLang(),
            plotOptions: {
                series: {
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return `<b>${this.point.name}</b> (${formatSoles(this.point.y)})`;
                        },
                        allowOverlap: true,
                        x: 10,
                        y: -5
                    },
                    width: '60%',
                    height: '80%',
                    center: ['50%', '45%']
                }
            },
            series: [{
                name: 'Monto',
                data: datos
            }]
        });

        removeCreditos();
    }

    function eventsCostCenterMonth() {
        document.querySelector('#excel-cost-center-month').addEventListener('click', (e) => {
            excelCostCenterMonth();
        })
    }

    function excelCostCenterMonth() {

        const url = @json(route('tenant.dashboard.dashboard.excelCentroCostosMes'));

        const params = {
            year: document.querySelector('#filtro_anio').value,
            month: document.querySelector('#filtro_mes').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;

    }
</script>
