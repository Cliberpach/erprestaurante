<style>
    body {
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

    #kpi_sales {
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


<figure class="highcharts-figure">
    <div id="kpi_sales"></div>
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

    function setKpiSales(_title, _subtitle, _data) {

        const formattedData = formatDataKS(_data);

        const chart = Highcharts.chart('kpi_sales', {
            chart: {
                type: 'column',
                options3d: {
                    enabled: true,
                    alpha: 15,
                    beta: 15,
                    viewDistance: 25,
                    depth: 40
                }
            },

            title: {
                text: _title
            },
            subtitle: {
                text: _subtitle
            },
            xAxis: {
                categories: ['DÍA', 'MES', 'AÑO'],
                labels: {
                    skew3d: true,
                    style: {
                        fontSize: '16px'
                    }
                }
            },

            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: 'Monto',
                    skew3d: true,
                    style: {
                        fontSize: '16px'
                    }
                }
            },

            tooltip: {
                formatter: function() {
                    return '<b>' + this.key + '</b><br>' +
                        '<span style="color:' + this.series.color + '">●</span> ' +
                        this.series.name + ': S/' + formatSoles(this.y);
                }
            },

            plotOptions: {
                column: {
                    stacking: 'normal',
                    depth: 40,
                    dataLabels: {
                        enabled: true,
                        formatter: function() {
                            return 'S/' + formatSoles(this.y);
                        }
                    }
                }
            },

            series: [{
                name: 'Actual',
                data: formattedData.lstNow,
                stack: 'Actual'
            }, {
                name: 'Anterior',
                data: formattedData.lstPrevious,
                stack: 'Anterior'
            }]
        });


        removeCreditos();

    }

    const formatDataKS = (data) => {
        const lstNow = [
            parseFloat(data.today_sales),
            parseFloat(data.sales_month),
            parseFloat(data.sales_year)
        ];

        const lstPrevious = [
            parseFloat(data.sales_yesterday),
            parseFloat(data.sales_previous_month),
            parseFloat(data.sales_previous_year)
        ];

        const formattedData = {
            lstNow,
            lstPrevious
        };

        return formattedData;
    }

    /*function eventsDishesMonth() {
        document.querySelector('#excel-dishes-month').addEventListener('click', (e) => {
            excelDishesMonth();
        })
    }

    function excelDishesMonth() {

        const url = @json(route('tenant.dashboard.dashboard.excelPlatosMes'));

        const params = {
            year: document.querySelector('#filtro_anio').value,
            month: document.querySelector('#filtro_mes').value,
        };

        const queryString = new URLSearchParams(params).toString();

        const finalUrl = `${url}?${queryString}`;
        window.location.href = finalUrl;

    }*/
</script>
