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

    #container-consumables-month {
        height: 400px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    #sliders {
        margin: 0.3rem 10px;
    }

    #sliders td input[type="range"] {
        display: inline;
    }

    #sliders td {
        padding-right: 1em;
        white-space: nowrap;
    }

    .highcharts-description {
        margin: 0.3rem 10px;
    }
</style>

<div class="d-flex justify-content-end">
    <button class="btn btn-success" id="excel-consumables-month-amount"><i class="fas fa-file-excel"></i> Excel</button>
</div>

<figure class="highcharts-figure">
    <div id="container-consumables-month"></div>
    <p class="highcharts-description">
    </p>
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
    function setConsumablesMonth(_title, subtitulo, _data) {

        const chart = new Highcharts.Chart('container-consumables-month', {
            chart: {
                type: 'pie',
                options3d: {
                    enabled: true,
                    alpha: 45,
                    beta: 0
                }
            },
            title: {
                text: _title
            },
            subtitle: {
                text: subtitulo
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            tooltip: {
                formatter: function() {
                    return `<b>${this.point.name}</b>: S/.${formatSoles(this.point.y)}`;
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    depth: 35,
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return `<b>${this.point.name}</b>: S/.${formatSoles(this.point.y)}`;
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Share',
                data: _data
            }],
            lang: getLang()
        });

        removeCreditos();
    }

    function eventConsumablesMonthAmount() {
        document.querySelector('#excel-consumables-month-amount').addEventListener('click', (e) => {
            excelConsumablesMonthAmount();
        })
    }

    function excelConsumablesMonthAmount() {

        const url = @json(route('tenant.dashboard.dashboard.excelConsumablesMonthAmount'));

        const params = {
            year: document.querySelector('#filtro_anio').value,
            month: document.querySelector('#filtro_mes').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;

    }
</script>
