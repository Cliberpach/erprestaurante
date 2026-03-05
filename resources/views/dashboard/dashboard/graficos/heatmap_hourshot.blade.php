<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

    .card {
        background: #ffffff;
        border: 1px solid #e5e9f2;
        border-radius: 20px;
        padding: 32px 36px;
        width: 100%;
        box-shadow: 0 4px 32px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .title-block {}

    .label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #ff6b35;
        margin-bottom: 6px;
    }

    h2 {
        font-family: 'Syne', sans-serif;
        font-size: 26px;
        font-weight: 800;
        color: #1a1f2e;
        line-height: 1.2;
    }

    .subtitle {
        font-size: 13px;
        color: #8a93b0;
        margin-top: 5px;
    }

    .stats {
        display: flex;
        gap: 24px;
    }

    .stat {
        text-align: right;
    }

    .stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: #1a1f2e;
    }

    .stat-label {
        font-size: 11px;
        color: #8a93b0;
        margin-top: 2px;
    }

    .stat-value.hot {
        color: #ff6b35;
    }

    .stat-value.cool {
        color: #38bdf8;
    }

    .filters {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-btn {
        background: #f4f6fa;
        border: 1px solid #e5e9f2;
        color: #8a93b0;
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        font-weight: 500;
        padding: 7px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #ff6b35;
        border-color: #ff6b35;
        color: #fff;
    }

    #heatmap-container {
        border-radius: 12px;
        overflow: hidden;
    }

    /* Highcharts overrides */
    .highcharts-background {
        fill: transparent !important;
    }

    .highcharts-plot-background {
        fill: transparent !important;
    }
</style>

<div class="card">
    <div class="card-header">
        <div class="title-block">
            <div class="label">Análisis de Tráfico</div>
            <h2>Horas Pico del Restaurante</h2>
            <div class="subtitle">Pedidos por hora y día de la semana — últimas 4 semanas</div>
        </div>
        <div class="stats">
            <div class="stat">
                <div class="stat-value hot" id="stat-peak">—</div>
                <div class="stat-label">Hora más activa</div>
            </div>
            <div class="stat">
                <div class="stat-value cool" id="stat-quiet">—</div>
                <div class="stat-label">Hora más tranquila</div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <select class="form-control" id="rango-heatmap" onchange="loadPeakHourAnalysis(this.value)">
                <option value="1">Último mes</option>
                <option value="2">Últimos 2 meses</option>
                <option value="3" selected>Últimos 3 meses</option>
            </select>
        </div>
    </div>

    <div class="filters">
        <button class="filter-btn active" onclick="setFilter(this, 'pedidos')">Pedidos</button>
        <button class="filter-btn" onclick="setFilter(this, 'ventas')">Ventas (S/)</button>
        <button class="filter-btn" onclick="setFilter(this, 'mesas')">Mesas Ocupadas</button>
    </div>

    <div id="heatmap-container"></div>
</div>

<script src="https://code.highcharts.com/modules/heatmap.js"></script>
<script>
    const datasets = {
        pedidos: [],
        ventas: [],
        mesas: []
    };


    const dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const horas = ['6am', '7am', '8am', '9am', '10am', '11am', '12pm',
        '1pm', '2pm', '3pm', '4pm', '5pm', '6pm', '7pm', '8pm', '9pm', '10pm', '11pm'
    ];

    let currentFilter = 'pedidos';
    let chart;

    function getPeakStats(data) {
        const horaSum = Array(18).fill(0);

        data.forEach(([x, y, v]) => {
            horaSum[x] += v; // x = hora, sumar todos los días
        });

        let maxVal = -Infinity,
            minVal = Infinity;
        let maxHora = '',
            minHora = '';

        horaSum.forEach((sum, i) => {
            if (sum > maxVal) {
                maxVal = sum;
                maxHora = horas[i];
            }
            if (sum < minVal && sum > 0) {
                minVal = sum;
                minHora = horas[i];
            } // > 0 para ignorar horas vacías
        });

        return {
            maxHora,
            minHora
        };
    }

    function getColorAxis(filter) {
        if (filter === 'ventas') return {
            min: 0,
            stops: [
                [0, '#eef2ff'],
                [0.3, '#bfdbfe'],
                [0.6, '#60a5fa'],
                [0.85, '#f59e0b'],
                [1, '#ef4444']
            ]
        };
        return {
            min: 0,
            stops: [
                [0, '#f0fdf4'],
                [0.25, '#bbf7d0'],
                [0.5, '#4ade80'],
                [0.75, '#f59e0b'],
                [1, '#ff6b35']
            ]
        };
    }

    function getTooltipLabel(filter) {
        if (filter === 'ventas') return 'S/ ';
        if (filter === 'mesas') return '';
        return '';
    }

    function getTooltipSuffix(filter) {
        if (filter === 'pedidos') return ' pedidos';
        if (filter === 'mesas') return ' mesas';
        return '';
    }

    function renderChart(filter) {
        const data = datasets[filter];
        const {
            maxHora,
            minHora
        } = getPeakStats(data);
        document.getElementById('stat-peak').textContent = maxHora;
        document.getElementById('stat-quiet').textContent = minHora;

        const maxVal = Math.max(...data.map(d => d[2]));
        const prefix = getTooltipLabel(filter);
        const suffix = getTooltipSuffix(filter);

        if (chart) chart.destroy();

        chart = Highcharts.chart('heatmap-container', {
            chart: {
                type: 'heatmap',
                backgroundColor: 'transparent',
                style: {
                    fontFamily: "'DM Sans', sans-serif"
                },
                height: 320,
                animation: {
                    duration: 600,
                    easing: 'easeOutQuart'
                },
                marginTop: 10,
                marginRight: 10,
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },

            xAxis: {
                categories: horas, // 6am … 11pm  →  índices 0-17
                labels: {
                    style: {
                        color: '#8a93b0',
                        fontSize: '11px',
                        fontFamily: "'DM Sans', sans-serif"
                    }
                },
                lineColor: 'transparent',
                tickColor: 'transparent',
                title: {
                    text: 'Hora del día',
                    style: {
                        color: '#b0b8cc',
                        fontSize: '11px'
                    }
                }
            },

            yAxis: {
                categories: dias, // Lun … Dom  →  índices 0-6
                reversed: false,
                labels: {
                    style: {
                        color: '#4a5068',
                        fontSize: '12px',
                        fontWeight: '500',
                        fontFamily: "'DM Sans', sans-serif"
                    }
                },
                lineColor: 'transparent',
                tickColor: 'transparent',
                title: {
                    text: ''
                },
                gridLineColor: 'transparent',
            },

            colorAxis: {
                ...getColorAxis(filter),
                max: maxVal,
                labels: {
                    style: {
                        color: '#8a93b0',
                        fontSize: '10px'
                    }
                }
            },

            legend: {
                align: 'right',
                layout: 'vertical',
                verticalAlign: 'middle',
                itemStyle: {
                    color: '#8a93b0',
                    fontSize: '10px'
                }
            },

            tooltip: {
                backgroundColor: '#ffffff',
                borderColor: '#e5e9f2',
                borderRadius: 10,
                shadow: {
                    color: 'rgba(0,0,0,0.12)',
                    offsetX: 0,
                    offsetY: 4,
                    opacity: 0.3,
                    width: 16
                },
                style: {
                    color: '#1a1f2e',
                    fontSize: '13px',
                    fontFamily: "'DM Sans', sans-serif"
                },
                formatter: function() {
                    const dia = dias[this.point.y];
                    const hora = horas[this.point.x];
                    const val = this.point.value;
                    const intensity = val / maxVal;
                    const emoji = intensity > 0.8 ? '🔥' : intensity > 0.5 ? '📈' : intensity > 0.25 ?
                        '🟡' : '💤';
                    return `<b style="color:#ff6b35">${dia}</b> · ${hora}<br/>
                  <span style="font-size:20px">${emoji}</span>
                  <b style="font-size:16px"> ${prefix}${val.toLocaleString()}${suffix}</b>`;
                }
            },

            series: [{
                name: filter,
                borderWidth: 2,
                borderColor: '#ffffff',
                borderRadius: 4,
                data: data,
                dataLabels: {
                    enabled: maxVal < 60,
                    color: 'rgba(0,0,0,0.35)',
                    style: {
                        fontSize: '9px',
                        fontWeight: '400',
                        textOutline: 'none'
                    },
                    formatter: function() {
                        return this.point.value > 0 ? this.point.value : '';
                    }
                }
            }],

            plotOptions: {
                heatmap: {
                    animation: {
                        duration: 600
                    }
                }
            }
        });
    }

    function setFilter(btn, filter) {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = filter;
        renderChart(filter);
    }

    async function loadPeakHourAnalysis(meses) {
        try {
            const res = await axios.get(route('tenant.dashboard.dashboard.peakHourAnalysis', {
                meses
            }));

            if (res.data.success) {

                dataPedidos = res.data.data.peak_hour_orders;
                datasets.pedidos = dataPedidos;

                dataSales = res.data.data.peak_hour_sales;
                datasets.ventas = dataSales;

                dataMesas = res.data.data.peak_hour_tables;
                datasets.mesas = dataMesas;
                renderChart('pedidos');

            } else {
                toastr.error(res.data.message, 'Error en el servidor al cargar horas pico');
            }
        } catch (error) {
            toastr.error(error, 'Error en la petición horas pico');
        }
    }
</script>
