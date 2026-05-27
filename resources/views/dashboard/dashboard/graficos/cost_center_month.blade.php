<div class="d-flex justify-content-end mb-2">
    <button class="btn btn-success btn-sm" id="excel-cost-center-month">
        <i class="fas fa-file-excel"></i> Excel
    </button>
</div>

<div id="cost_center_month"></div>

<script>
    function setCostCenterMonth(titulo, subtitulo, datos) {
        // datos: [['Centro A', 1500], ['Centro B', 900], ...]
        // Ordenar de mayor a menor para que el más grande quede arriba
        const sorted = [...datos].sort((a, b) => b[1] - a[1]);

        const categorias = sorted.map(d => d[0]);
        const valores    = sorted.map(d => d[1]);

        Highcharts.chart('cost_center_month', {
            chart: {
                type:           'bar',
                marginLeft:     180,
                animation:      true,
            },
            title: {
                text:  titulo,
                align: 'left',
                style: { fontSize: '13px', fontWeight: 'bold' }
            },
            subtitle: {
                text:  subtitulo || '',
                align: 'left'
            },
            lang: getLang(),
            xAxis: {
                categories: categorias,
                title:      { text: null },
                labels:     {
                    style:    { fontSize: '12px' },
                    overflow: 'justify'
                }
            },
            yAxis: {
                min:   0,
                title: { text: 'Monto (S/)', align: 'high' },
                labels: {
                    formatter: function () {
                        return 'S/ ' + Highcharts.numberFormat(this.value, 0, '.', ',');
                    }
                },
                gridLineDashStyle: 'ShortDash'
            },
            tooltip: {
                formatter: function () {
                    return `<b>${this.point.category}</b><br/>` + formatSoles(this.y);
                }
            },
            plotOptions: {
                bar: {
                    colorByPoint:  true,
                    borderRadius:  3,
                    dataLabels: {
                        enabled:   true,
                        align:     'left',
                        inside:    false,
                        formatter: function () { return formatSoles(this.y); },
                        style:     { fontSize: '11px', fontWeight: 'normal', textOutline: 'none' }
                    }
                }
            },
            legend: { enabled: false },
            series: [{
                name: 'Monto',
                data: valores
            }],
            credits: { enabled: false },
            exporting: {
                buttons: { contextButton: { menuItems: ['downloadPNG', 'downloadPDF', 'downloadCSV'] } }
            }
        });

        removeCreditos();
    }

    function eventsCostCenterMonth() {
        document.querySelector('#excel-cost-center-month').addEventListener('click', () => {
            excelCostCenterMonth();
        });
    }

    function excelCostCenterMonth() {
        const url    = @json(route('tenant.dashboard.dashboard.excelCentroCostosMes'));
        const params = {
            year:  document.querySelector('#filtro_anio').value,
            month: document.querySelector('#filtro_mes').value,
        };
        window.location.href = `${url}?${new URLSearchParams(params).toString()}`;
    }
</script>
