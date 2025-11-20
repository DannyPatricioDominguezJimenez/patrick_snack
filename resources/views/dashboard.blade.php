<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #1f2937;">
            {{ __('Dashboard Principal') }}
        </h2>
    </x-slot>

    {{-- LIBRERÍA DE GRÁFICOS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .dashboard-container { max-width: 1300px; margin: 0 auto; padding: 20px; }
        .kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; margin-bottom: 30px; }
        .kpi-card { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #ccc; }
        .kpi-card h4 { font-size: 0.9em; text-transform: uppercase; color: #666; margin-bottom: 5px; }
        .kpi-card p { font-size: 1.8em; font-weight: bold; color: #333; margin: 0; }
        
        /* Colores KPI */
        .kpi-green { border-left-color: #198754; }
        .kpi-blue { border-left-color: #0d6efd; }
        .kpi-red { border-left-color: #dc3545; }
        .kpi-purple { border-left-color: #6f42c1; }
        
        .charts-section { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .chart-box { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .chart-box h3 { font-size: 1.2em; font-weight: 600; margin-bottom: 15px; text-align: center; }
    </style>

    <div class="py-12">
        <div class="dashboard-container">
            
            {{-- VERIFICACIÓN DE SEGURIDAD: Si la variable no llega, mostramos mensaje --}}
            @if(!isset($dashboardData))
                <div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px;">
                    Error: No hay datos disponibles. Revisa el Controlador.
                </div>
            @else

            {{-- 1. KPIs --}}
            <div class="kpi-grid">
                <div class="kpi-card kpi-green">
                    <h4>Ventas Totales</h4>
                    <p>${{ $dashboardData['kpis']['totalSales'] }}</p>
                </div>
                <div class="kpi-card kpi-green">
                    <h4>Mes Actual</h4>
                    <p>${{ $dashboardData['kpis']['salesLastMonth'] }}</p>
                </div>
                <div class="kpi-card kpi-purple">
                    <h4>Productos</h4>
                    <p>{{ $dashboardData['kpis']['totalProducts'] }}</p>
                </div>
                <div class="kpi-card kpi-blue">
                    <h4>Nuevos Clientes</h4>
                    <p>{{ $dashboardData['kpis']['newClients'] }}</p>
                </div>
                <div class="kpi-card kpi-red">
                    <h4>Stock Agotado</h4>
                    <p>{{ $dashboardData['kpis']['outOfStock'] }}</p>
                </div>
            </div>

            {{-- 2. GRÁFICOS --}}
            <div class="charts-section">
                <div class="chart-box" style="grid-column: span 2;">
                    <h3>Histórico de Ventas ($)</h3>
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>

                <div class="chart-box">
                    <h3>Ventas por Categoría de Cliente</h3>
                    <canvas id="categoryChart" height="200"></canvas>
                </div>

                <div class="chart-box">
                    <h3>Top 5 Productos (Unidades)</h3>
                    <canvas id="productChart" height="200"></canvas>
                </div>
            </div>

            @endif
        </div>
    </div>

    <script>
        // Pasar datos de PHP a JS de forma segura
        const DATA = @json($dashboardData ?? []);
        const CHARTS = DATA.charts || {};

        document.addEventListener('DOMContentLoaded', function() {
            if (!DATA.kpis) return; // Si no hay datos, no hacemos nada

            // 1. Gráfico Mensual (Línea)
            new Chart(document.getElementById('monthlyChart'), {
                type: 'line',
                data: {
                    labels: CHARTS.monthly.labels,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: CHARTS.monthly.data,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true }
            });

            // 2. Gráfico Categorías (Donut)
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: CHARTS.categories.labels,
                    datasets: [{
                        data: CHARTS.categories.data,
                        backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545', '#6f42c1']
                    }]
                }
            });

            // 3. Gráfico Productos (Barras)
            new Chart(document.getElementById('productChart'), {
                type: 'bar',
                data: {
                    labels: CHARTS.products.labels,
                    datasets: [{
                        label: 'Unidades',
                        data: CHARTS.products.data,
                        backgroundColor: '#198754'
                    }]
                },
                options: { indexAxis: 'y' } // Barras horizontales
            });
        });
    </script>
</x-app-layout>