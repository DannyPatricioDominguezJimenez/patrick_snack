<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class DailyLogController extends Controller
{
    /**
     * Muestra la vista principal, listando actividades con paginación y filtro por rango de fechas.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $today = now()->toDateString();
        
        // 1. Determinar el rango de fechas
        $startDate = $request->input('start_date', $today);
        $endDate = $request->input('end_date', $today);

        // Asegurarse de que las fechas sean válidas
        try {
            $startDate = Carbon::parse($startDate)->toDateString();
            $endDate = Carbon::parse($endDate)->toDateString();
        } catch (\Exception $e) {
            // Fallback: si las fechas son inválidas, usamos el día de hoy.
            $startDate = $today;
            $endDate = $today;
        }

        // 2. Cargar actividades con PAGINACIÓN (10 en 10)
        $activities = DailyLog::whereBetween('activity_date', [$startDate, $endDate])
                       ->where('user_id', $userId)
                       ->orderBy('activity_date', 'desc') // Ordenar por fecha (más reciente primero)
                       ->orderBy('created_at', 'asc')
                       ->paginate(10)
                       ->appends($request->query()); // Mantiene los filtros en los enlaces de paginación

        // 3. Respuesta normal para la vista principal
        return view('vistas.calendario', [
            'activities' => $activities,
            'startDate' => $startDate, // Fecha de inicio del filtro
            'endDate' => $endDate      // Fecha de fin del filtro
        ]);
    }
    
    /**
     * Crea un nuevo log diario (CREATE).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_date' => 'required|date',
            'description' => 'required|string',
        ]);
        
        $date = $validated['activity_date'];

        DailyLog::create([
            'activity_date' => $date,
            'description' => $validated['description'],
            'user_id' => Auth::id(),
        ]);
        
        $message = 'Actividad guardada para el ' . Carbon::parse($date)->format('d/m/Y') . '.';

        // 💡 Redirigimos, pero intentamos mantener los parámetros de filtro si existen.
        $params = $request->only(['start_date', 'end_date', 'page']);
        return Redirect::route('calendario.index', $params)->with('success', $message);
    }
    
    /**
     * Actualiza un log diario específico (UPDATE).
     */
    public function update(Request $request, DailyLog $dailyLog)
    {
        $validated = $request->validate([
            'description' => 'required|string', 
        ]);

        $dailyLog->update($validated);

        // 💡 Redirigimos, pero intentamos mantener los parámetros de filtro si existen.
        $params = $request->only(['start_date', 'end_date', 'page']);
        return Redirect::route('calendario.index', $params)->with('success', 'Actividad actualizada correctamente.');
    }

    /**
     * Elimina el log diario específico (DELETE).
     */
    public function destroy(DailyLog $dailyLog, Request $request)
    {
        $dailyLog->delete();

        // 💡 Redirigimos, pero intentamos mantener los parámetros de filtro si existen.
        $params = $request->only(['start_date', 'end_date', 'page']);
        return Redirect::route('calendario.index', $params)->with('success', 'Actividad eliminada correctamente.');
    }
}