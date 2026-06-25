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
    }

    #container-products-month {
        height: 400px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    #sliders-products-month {
        margin: 0.3rem 10px;
    }

    #sliders-products-month td input[type="range"] {
        display: inline;
    }

    #sliders-products-month td {
        padding-right: 1em;
        white-space: nowrap;
    }

    .highcharts-description {
        margin: 0.3rem 10px;
    }
</style>

<div class="d-flex justify-content-end">
    <button class="btn btn-success" id="excel-products-month"><i class="fas fa-file-excel"></i> Excel</button>
</div>

<figure class="highcharts-figure">
    <div id="container-products-month"></div>
    <p class="highcharts-description">

    </p>
    <div id="sliders-products-month">
        <table>
            <tr>
                <td><label for="alpha">Alpha Angle</label></td>
                <td><input id="alpha" type="range" min="0" max="45" value="15" /> <span
                        id="alpha-value-products-month" class="value"></span></td>
            </tr>
            <tr>
                <td><label for="beta">Beta Angle</label></td>
                <td><input id="beta" type="range" min="-45" max="45" value="15" /> <span
                        id="beta-value-products-month" class="value"></span></td>
            </tr>
            <tr>
                <td><label for="depth">Depth</label></td>
                <td><input id="depth" type="range" min="20" max="100" value="50" /> <span
                        id="depth-value-products-month" class="value"></span></td>
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
    function setProductosMes(titulo, subtitulo, datos) {
        const chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container-products-month',
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
                pointFormat: 'Cant: {point.y}'
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
                        format: '{point.y}',
                        style: {
                            fontSize: '11px',
                            fontWeight: 'bold'
                        }
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
            '#sliders-products-month input'
        ).forEach(input => input.addEventListener('input', e => {
            chart.options.chart.options3d[e.target.id] = parseFloat(e.target.value);
            showValuesProductsMonth(chart);
            chart.redraw(false);
        }));

        removeCreditos();
    }

    function showValuesProductsMonth(chart) {
        document.getElementById(
            'alpha-value-products-month'
        ).innerHTML = chart.options.chart.options3d.alpha;
        document.getElementById(
            'beta-value-products-month'
        ).innerHTML = chart.options.chart.options3d.beta;
        document.getElementById(
            'depth-value-products-month'
        ).innerHTML = chart.options.chart.options3d.depth;
    }

    function eventsProductsMonth() {
        document.querySelector('#excel-products-month').addEventListener('click', (e) => {
            excelProductsMonth();
        })
    }

    function excelProductsMonth() {

        const url = @json(route('tenant.dashboard.dashboard.excelProductosMes'));

        const params = {
            year: document.querySelector('#filtro_anio').value,
            month: document.querySelector('#filtro_mes').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;

    }
</script>
