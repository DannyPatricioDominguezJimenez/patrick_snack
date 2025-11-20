<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Client;
use App\Models\Stock;
use App\Models\Product;
use App\Models\SaleDetail; // Asegúrate de tener este modelo o usar DB table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // --- 1. KPIs (Indicadores) ---
        // Usamos 'floatval' para asegurar que sea un número, incluso si la BD devuelve null string
        $totalSales = floatval(Sale::sum('total_amount'));
        $salesLastMonth = floatval(Sale::where('sale_date', '>=', now()->startOfMonth())->sum('total_amount'));
        $outOfStock = Stock::where('quantity', 0)->count();
        $newClients = Client::where('created_at', '>=', now()->subDays(30))->count();
        $totalProducts = Product::count();

        // Estructura de KPIs lista para la vista
        $kpis = [
            'totalSales' => number_format($totalSales, 2),
            'salesLastMonth' => number_format($salesLastMonth, 2),
            'outOfStock' => $outOfStock,
            'newClients' => $newClients,
            'totalProducts' => $totalProducts
        ];

        // --- 2. DATOS PARA GRÁFICOS ---

        // Gráfico 1: Ventas por Categoría
        $salesByClientCategory = DB::table('sales')
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.id')
            ->leftJoin('client_categories', 'clients.client_category_id', '=', 'client_categories.id')
            ->select(
                DB::raw('COALESCE(client_categories.name, "Sin Categoría") as category_name'),
                DB::raw('COALESCE(SUM(sales.total_amount), 0) as total')
            )
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        // Gráfico 2: Top Productos
        $topSellingProducts = DB::table('sale_details')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->select('products.name as product_name', DB::raw('SUM(sale_details.quantity) as total_quantity'))
            ->groupBy('products.name') // Agrupar por nombre explícitamente
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Gráfico 3: Histórico Mensual (Últimos 6 meses para asegurar datos visibles)
        $monthlySalesRaw = Sale::select(
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('YEAR(sale_date) as year'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('sale_date', '>=', now()->subMonths(6))
            ->groupBy('month', 'year')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Formatear fechas para el gráfico
        $monthlySalesLabels = [];
        $monthlySalesValues = [];
        foreach ($monthlySalesRaw as $data) {
            $monthlySalesLabels[] = Carbon::create(null, $data->month)->shortLocaleMonth . ' ' . $data->year;
            $monthlySalesValues[] = $data->total;
        }

        // --- 3. EMPAQUETAR TODO ---
        $dashboardData = [
            'kpis' => $kpis,
            'charts' => [
                'categories' => [
                    'labels' => $salesByClientCategory->pluck('category_name'),
                    'data' => $salesByClientCategory->pluck('total')
                ],
                'products' => [
                    'labels' => $topSellingProducts->pluck('product_name'),
                    'data' => $topSellingProducts->pluck('total_quantity')
                ],
                'monthly' => [
                    'labels' => $monthlySalesLabels,
                    'data' => $monthlySalesValues
                ]
            ]
        ];

        return view('dashboard', compact('dashboardData'));
    }
}