<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #1f2937;">
            {{ __('Gestión de Actividades Diarias') }}
        </h2>
    </x-slot>

    {{-- ***************************************************************** --}}
    {{-- ******************* ESTILOS CSS BASE Y MODAL ******************** --}}
    {{-- ***************************************************************** --}}
    <style>
        /* BASE Y LAYOUT */
        .crud-container { max-width: 950px; margin: 0 auto; padding: 20px; }
        .card { background-color: #fff; border-radius: 14px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1); padding: 35px; margin-top: 30px; border: 1px solid #e0e0e0; }
        
        /* BOTONES MODAL */
        .btn-base { border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background-color: #0d6efd; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-cancel { background-color: #f8f9fa; color: #333; border: 1px solid #ccc; }
        
        /* Contenedor de Fecha y Acciones */
        .header-controls { display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px; border-bottom: 1px solid #eee; margin-bottom: 25px; }
        .date-input-area input[type="date"] { padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 1.1em; cursor: pointer; }
        
        /* TABLA DE ACTIVIDADES */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .data-table th, .data-table td { padding: 16px; text-align: left; }
        .data-table thead th { background-color: #e9ecef; color: #343a40; font-weight: 700; text-transform: uppercase; font-size: 0.9em; }
        .data-table tbody tr:hover { background-color: #f8f8f8; }

        /* MODALES */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: none; justify-content: center; align-items: center; z-index: 1000; opacity: 0; transition: opacity 0.3s ease; }
        .modal.active { opacity: 1; display: flex; }
        .modal-content { background-color: white; padding: 40px; border-radius: 12px; width: 90%; max-width: 600px; transform: scale(0.9); transition: transform 0.3s ease-out; }
        .modal-content h3 { font-size: 1.8em; margin-bottom: 20px; color: #333; font-weight: 700; }
        .modal-content textarea { width: 100%; min-height: 150px; padding: 15px; border: 1px solid #ccc; border-radius: 6px; font-size: 1em; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
        .error-message { color: #dc3545; font-weight: 600; margin-bottom: 10px; }
        
        /* Estilos de Paginación para que se vean bien */
        .pagination { display: flex; padding-left: 0; list-style: none; border-radius: 0.25rem; }
        .pagination li a, .pagination li span { position: relative; display: block; padding: 0.5rem 0.75rem; margin-left: -1px; line-height: 1.25; color: #007bff; background-color: #fff; border: 1px solid #dee2e6; }
        .pagination li.active span { z-index: 1; color: #fff; background-color: #007bff; border-color: #007bff; }
    </style>

    <div class="py-12">
        <div class="crud-container">
            <div class="card">
                <h3 style="font-size: 1.8em; font-weight: 700; color: #333; margin-bottom: 20px;">
                    Gestión de Actividades Diarias
                </h3>
                
                {{-- MENSAJES DE ÉXITO --}}
                @if(session('success'))
                    <div style="background-color: #d4edda; color: #0f5132; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;">
                        ✅ {{ session('success') }}
                    </div>
                @endif
                
                {{-- ⬅️ FORMULARIO DE FILTRO POR RANGO DE FECHAS Y BOTÓN DE CREACIÓN --}}
                <div class="header-controls">
                    <form method="GET" action="{{ route('calendario.index') }}" id="filterForm" style="display: flex; align-items: center; gap: 15px;">
                        
                        <label style="font-weight: 600; color: #343a40;">Desde:</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            value="{{ $startDate }}"
                            required
                            style="padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 1em;"
                        >
                        
                        <label style="font-weight: 600; color: #343a40;">Hasta:</label>
                        <input 
                            type="date" 
                            name="end_date" 
                            value="{{ $endDate }}"
                            required
                            style="padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 1em;"
                        >
                        
                        <button type="submit" class="btn-base btn-primary" style="padding: 10px 20px;">
                            🔍 Filtrar
                        </button>
                    </form>

                    {{-- Botón CREATE --}}
                    <button onclick="openCreateModal('{{ $startDate }}')" class="btn-base btn-primary">
                        + Nueva Actividad
                    </button>
                </div>

                {{-- TABLA DE ACTIVIDADES (READ) --}}
                <div class="log-display">
                    <h4 style="font-weight: 700; color: #0d6efd; margin-bottom: 15px;">
                        Actividades Registradas (Rango: {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} a {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }})
                    </h4>
                    
                    <div style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th> 
                                    <th>Descripción</th>
                                    <th style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->id }}</td>
                                        <td>{{ $activity->activity_date->format('d/m/Y') }}</td> 
                                        <td>{{ Str::limit($activity->description, 100) }}</td>
                                        <td style="display: flex; gap: 5px;">
                                            {{-- Botón EDITAR --}}
                                            <button onclick="openEditModal({{ $activity }})" class="btn-base" style="color: #0dcaf0; padding: 8px;">✏️ Editar</button>
                                            
                                            {{-- Botón ELIMINAR --}}
                                            <form action="{{ route('diariolog.destroy', $activity) }}" method="POST" onsubmit="return confirm('¿Eliminar esta actividad?');">
                                                @csrf @method('DELETE')
                                                {{-- Pasamos los filtros ocultos para mantener el contexto después de la eliminación --}}
                                                <input type="hidden" name="start_date" value="{{ $startDate }}">
                                                <input type="hidden" name="end_date" value="{{ $endDate }}">
                                                <input type="hidden" name="page" value="{{ $activities->currentPage() }}">
                                                <button type="submit" class="btn-base" style="color: #dc3545; padding: 8px;">🗑️ Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: #888; padding: 30px;">
                                            No hay actividades registradas para este rango de fechas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- CONTROLES DE PAGINACIÓN --}}
                    <div style="margin-top: 25px; display: flex; justify-content: flex-end;">
                        {{-- Mantiene los parámetros de fecha de inicio y fin en la URL de paginación --}}
                        {{ $activities->appends(['start_date' => $startDate, 'end_date' => $endDate])->links() }} 
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ***************************************************************** --}}
    {{-- ******************** MODALES CRUD DE ACTIVIDADES ****************** --}}
    {{-- ***************************************************************** --}}

    {{-- MODAL CREAR ACTIVIDAD (C) --}}
    <div id="createModal" class="modal">
        <div class="modal-content">
            <h3 id="createModalTitle">Nueva Actividad</h3>
            {{-- La acción incluye los filtros de contexto para el redirect --}}
            <form id="createForm" method="POST" action="{{ route('diariolog.store', ['start_date' => $startDate, 'end_date' => $endDate, 'page' => $activities->currentPage()]) }}">
                @csrf
                <input type="hidden" id="createActivityDate" name="activity_date" value="{{ $startDate }}">
                
                <div class="form-group">
                    <label for="createDescription" style="display: block; font-weight: 600; margin-bottom: 8px;">Descripción:</label>
                    <textarea id="createDescription" name="description" placeholder="Escribe la actividad realizada..." required>{{ old('description') }}</textarea>
                    @error('description') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('createModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDITAR ACTIVIDAD (U) --}}
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3 id="editModalTitle">Editar Actividad</h3>
            {{-- La acción se establecerá dinámicamente con el ID y los filtros ocultos --}}
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="editActivityDate" name="activity_date">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <input type="hidden" name="page" value="{{ $activities->currentPage() }}">
                
                <div class="form-group">
                    <label for="editDescription" style="display: block; font-weight: 600; margin-bottom: 8px;">Descripción:</label>
                    <textarea id="editDescription" name="description" required></textarea>
                    @error('description') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('editModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
    
    
    {{-- ***************************************************************** --}}
    {{-- ************************ SCRIPTS JS ***************************** --}}
    {{-- ***************************************************************** --}}
    <script>
        // Funciones de Modal Base (Reutilizadas)
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }
        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('active');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        // Utilidad para formatear la fecha
        function formatDateDisplay(dateString) {
            const date = new Date(dateString + 'T00:00:00');
            return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
        }

        // 1. Manejar la recarga de página al cambiar el filtro
        // El formulario de filtro (id="filterForm") ya maneja la recarga al hacer submit.

        // 2. Abrir Modal de Creación
        function openCreateModal(date) {
            // Establece la fecha de inicio del filtro como fecha predeterminada para la nueva actividad
            const selectedDate = document.querySelector('input[name="start_date"]').value;
            
            document.getElementById('createActivityDate').value = selectedDate;
            document.getElementById('createModalTitle').textContent = `Nueva Actividad para: ${formatDateDisplay(selectedDate)}`;
            document.getElementById('createDescription').value = ''; 
            openModal('createModal');
        }

        // 3. Abrir Modal de Edición
        function openEditModal(activity) {
            const date = activity.activity_date; 
            
            document.getElementById('editActivityDate').value = date;
            document.getElementById('editModalTitle').textContent = `Editar Actividad (${activity.id}) para: ${formatDateDisplay(date)}`;
            
            // Rellenar campos
            document.getElementById('editDescription').value = activity.description;
            
            // Establecer la URL de acción para la ACTUALIZACIÓN (PUT)
            document.getElementById('editForm').action = `{{ url('diariolog') }}/${activity.id}`;

            openModal('editModal');
        }
        
        // 4. Manejar la reapertura del modal si la validación falla (Laravel)
        @if ($errors->any())
            // Detecta si el error fue en la creación o edición
            @php
                $isEdit = old('_method') === 'PUT';
            @endphp
            
            // Reabrir el modal correcto
            openModal('{{ $isEdit ? 'editModal' : 'createModal' }}');
            
            // Si es edición, rellenar el formulario con los datos fallidos y restaurar la acción
            @if ($isEdit)
                // Usamos el ID de la ruta original que está en la URL de errores (si se maneja bien en el controlador)
                // Como pasamos el ID a la URL de error, lo podemos recuperar si es necesario, pero es mejor usar old() si el controlador lo pasa.
                
                // Si el controlador pasó el ID de vuelta:
                // const activityId = "{{ old('activity_log_id') }}";
                // document.getElementById('editForm').action = `{{ url('diariolog') }}/${activityId}`;
                
                // Simplemente rellenamos la descripción (ya que el ID no cambia en el PUT)
                document.getElementById('editDescription').value = `{{ old('description') }}`;
            @endif
        @endif
    </script>
</x-app-layout>