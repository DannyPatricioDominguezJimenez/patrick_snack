<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #1f2937;">
            {{ __('Gestión de Clientes') }}
        </h2>
    </x-slot>

    {{-- ***************************************************************** --}}
    {{-- ******************* ESTILOS CSS FINALES ************************* --}}
    {{-- ***************************************************************** --}}
    <style>
        /* BASE Y LAYOUT */
        .crud-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { 
            background-color: #fff; 
            border-radius: 14px; 
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1); 
            padding: 35px; 
            margin-top: 30px; 
            border: 1px solid #e0e0e0;
        }
        
        /* BOTONES GENERALES */
        .btn-base { border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; text-transform: uppercase; letter-spacing: 0.5px; }
        .btn-primary { background-color: #0d6efd; color: white; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4); }
        .btn-primary:hover { background-color: #0b5ed7; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(13, 110, 253, 0.6); }
        .btn-secondary { background-color: #6f42c1; color: white; margin-left: 10px; }
        .btn-secondary:hover { background-color: #5936a7; transform: translateY(-1px); }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-danger:hover { background-color: #bb2d3b; transform: translateY(-1px); }
        .btn-clear { background-color: #6c757d; color: white; }
        .btn-clear:hover { background-color: #5a6268; }

        /* FILTROS */
        .filter-form { background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; border: 1px solid #e9ecef; }
        .filter-form input, .filter-form select { padding: 12px; border: 1px solid #ced4da; border-radius: 6px; width: 280px; }
        .filter-form input:focus, .filter-form select:focus { border-color: #0d6efd; outline: none; box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15); }

        /* TABLA */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .data-table th, .data-table td { padding: 16px; text-align: left; }
        .data-table thead th { background-color: #e9ecef; color: #343a40; font-weight: 700; border-bottom: 2px solid #dee2e6; text-transform: uppercase; font-size: 0.9em; }
        .data-table tbody tr { border-bottom: 1px solid #f1f1f1; transition: background-color 0.2s ease; }
        .data-table tbody tr:hover { background-color: #e6f7ff; }

        /* ETIQUETAS */
        .tag { display: inline-block; padding: 6px 14px; border-radius: 25px; font-size: 0.8em; font-weight: 700; }
        
        /* ACCIONES */
        .actions button { background: none; border: none; padding: 5px 8px; cursor: pointer; font-size: 1.2em; margin-right: 5px; transition: color 0.2s; }
        .actions .btn-edit { color: #0dcaf0; }
        .actions .btn-delete-icon { color: #dc3545; }

        /* MODALES */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: none; justify-content: center; align-items: center; z-index: 1000; opacity: 0; transition: opacity 0.3s ease; }
        .modal.active { opacity: 1; display: flex; }
        .modal-content { background-color: white; padding: 40px; border-radius: 12px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4); width: 90%; max-width: 500px; transform: scale(0.9); transition: transform 0.3s ease-out; }
        .modal.active .modal-content { transform: scale(1); }
        .modal-content h3 { font-size: 2em; margin-bottom: 25px; color: #333; font-weight: 700; }
        .modal-content input, .modal-content select, .modal-content textarea { width: 100%; padding: 14px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 15px; box-sizing: border-box; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
        .btn-cancel { background-color: #f8f9fa; color: #333; border: 1px solid #ccc; }

        /* Estilo para el modal de categorías */
        .category-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .category-table th, .category-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .category-actions button { font-size: 0.9em; margin-left: 5px; }
    </style>

    <div class="py-12">
        <div class="crud-container">
            <div class="card">
                <div style="padding: 10px;">

                    {{-- ENCABEZADO Y BOTONES DE ACCIÓN --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px; border-bottom: 1px solid #eee; margin-bottom: 25px;">
                        <h3 style="font-size: 1.8em; font-weight: 700; color: #333;">Clientes</h3>
                        <div>
                            {{-- BOTÓN PARA ABRIR EL CRUD DE CATEGORÍAS --}}
                            <button onclick="openModal('manageCategoriesModal')" class="btn-base btn-secondary">
                                🏷️ Administrar Categorías
                            </button>
                            {{-- BOTÓN PARA CREAR CLIENTE --}}
                            <button onclick="openModal('createModal')" class="btn-base btn-primary">
                                + Agregar Cliente
                            </button>
                        </div>
                    </div>

                    {{-- FILTROS --}}
                    <form method="GET" action="{{ route('clientes.index') }}" class="filter-form">
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar Cédula/RUC, Nombre o Email...">
                        
                        {{-- FILTRO POR CATEGORÍA --}}
                        <select name="category_id">
                            <option value="">-- Filtrar por Categoría --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="btn-base btn-primary" style="padding: 12px 20px;">Aplicar</button>
                        <a href="{{ route('clientes.index') }}" class="btn-base btn-clear" style="padding: 12px 20px;">Limpiar</a>
                    </form>

                    {{-- MENSAJES Y ERRORES --}}
                    @if(session('success'))
                        <div style="background-color: #d4edda; color: #0f5132; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;">
                            ✅ {{ session('success') }}
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div style="background-color: #f8d7da; color: #842029; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c2c7;">
                            ⚠️ Error de validación: Por favor, revisa los campos en el modal.
                        </div>
                    @endif

                    {{-- TABLA DE CLIENTES (R) --}}
                    <div style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cédula/RUC</th> {{-- ⬅️ Encabezado actualizado --}}
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Categoría</th> 
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientes as $cliente)
                                    <tr>
                                        <td>{{ $cliente->id }}</td>
                                        <td style="font-weight: 600;">{{ $cliente->cedula }}</td>
                                        <td>{{ $cliente->nombre }}</td>
                                        <td>{{ $cliente->email }}</td>
                                        <td>{{ $cliente->telefono }}</td>
                                        <td>
                                            @if($cliente->category)
                                            <span class="tag" style="background-color: {{ $cliente->category->color_code ?? '#ccc' }}; color: {{ $cliente->category->color_code ? (strpos($cliente->category->color_code, '#000') !== false ? 'white' : 'black') : '#333' }};">
                                                {{ $cliente->category->name }}
                                            </span>
                                            @else
                                                <span class="tag" style="background-color: #f8d7da; color: #842029;">Sin Categoría</span>
                                            @endif
                                        </td>
                                        <td class="actions">
                                            <button onclick="openEditModal({{ $cliente }})" class="btn-edit" title="Editar">✏️</button>
                                            <button onclick="openDeleteModal({{ $cliente }})" class="btn-delete-icon" title="Eliminar">🗑️</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" style="text-align: center; color: #6c757d; padding: 30px; background-color: #fcfcfc;">No se encontraron clientes con los filtros aplicados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div style="margin-top: 25px; display: flex; justify-content: flex-end;">
                        {{ $clientes->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ***************************************************************** --}}
    {{-- ******************* MODALES CRUD DE CLIENTES ******************** --}}
    {{-- ***************************************************************** --}}

    {{-- MODAL CREAR CLIENTE (C) --}}
    <div id="createModal" class="modal">
        <div class="modal-content">
            <h3>Crear Nuevo Cliente</h3>
            <form method="POST" action="{{ route('clientes.store') }}">
                @csrf
                <input type="text" name="cedula" placeholder="Cédula o RUC (13 dígitos)" value="{{ old('cedula') }}" maxlength="13">
                <input type="text" name="nombre" placeholder="Nombre completo" required value="{{ old('nombre') }}">
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
                
                <select name="client_category_id">
                    <option value="">Seleccionar Categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('client_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                
                <input type="text" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}">
                <input type="text" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}">

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('createModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDITAR CLIENTE (U) --}}
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Editar Cliente</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="text" id="editCedula" name="cedula" placeholder="Cédula o RUC (13 dígitos)" maxlength="13">
                <input type="text" id="editNombre" name="nombre" placeholder="Nombre completo" required>
                <input type="email" id="editEmail" name="email" placeholder="Email">
                
                <select id="editCategory" name="client_category_id">
                    <option value="">Seleccionar Categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                
                <input type="text" id="editTelefono" name="telefono" placeholder="Teléfono">
                <input type="text" id="editDireccion" name="direccion" placeholder="Dirección">

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('editModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL ELIMINAR CLIENTE (D) --}}
    <div id="deleteModal" class="modal">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <h3 style="margin-bottom: 10px; color: #dc3545;">¿Confirmar Eliminación?</h3>
            <p>Estás a punto de eliminar a: <strong id="deleteClientName" style="color: #000;"></strong>. Esta acción no se puede deshacer.</p>
            
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-footer" style="justify-content: space-between; margin-top: 30px;">
                    <button type="button" onclick="closeModal('deleteModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-danger">Sí, Eliminar Cliente</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ***************************************************************** --}}
    {{-- ******************* MODALES CRUD DE CATEGORÍAS ****************** --}}
    {{-- ***************************************************************** --}}

    {{-- MODAL PRINCIPAL: GESTIÓN DE CATEGORÍAS (CRUD R) --}}
    <div id="manageCategoriesModal" class="modal">
        <div class="modal-content" style="max-width: 650px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3>Administrar Categorías</h3>
                <button onclick="openModal('createCategoryModal')" class="btn-base btn-primary" style="padding: 8px 15px;">+ Nueva Categoría</button>
            </div>

            <div style="max-height: 400px; overflow-y: auto;">
                <table class="category-table">
                    <thead>
                        <tr style="background-color: #f1f1f1;">
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Color</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    <span style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background-color: {{ $category->color_code ?? '#ccc' }}; border: 1px solid #ccc;"></span>
                                    <span style="color: {{ $category->color_code ?? '#333' }};">{{ $category->color_code }}</span>
                                </td>
                                <td class="category-actions">
                                    <button onclick="openEditCategoryModal({{ $category }})" class="btn-edit">✏️</button>
                                    
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('ATENCIÓN: Eliminar categoría? Esto quitará la categoría a los clientes asociados.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete-icon">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; color: #6c757d; padding: 20px;">No hay categorías registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="modal-footer" style="border-top: 1px solid #eee; padding-top: 20px;">
                <button type="button" onclick="closeModal('manageCategoriesModal')" class="btn-base btn-cancel">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- SUB-MODAL: CREAR CATEGORÍA (C) --}}
    <div id="createCategoryModal" class="modal">
        <div class="modal-content" style="max-width: 350px;">
            <h3>Crear Categoría</h3>
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <input type="text" name="name" placeholder="Nombre de la Categoría" required value="{{ old('name') }}">
                <input type="text" name="color_code" placeholder="Código de Color (ej: #0d6efd)" value="{{ old('color_code') }}">
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('createCategoryModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SUB-MODAL: EDITAR CATEGORÍA (U) --}}
    <div id="editCategoryModal" class="modal">
        <div class="modal-content" style="max-width: 350px;">
            <h3>Editar Categoría</h3>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <input type="text" id="editCategoryName" name="name" placeholder="Nombre de la Categoría" required>
                <input type="text" id="editCategoryColor" name="color_code" placeholder="Código de Color (ej: #0d6efd)">
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('editCategoryModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ***************************************************************** --}}
    {{-- ************************ SCRIPTS JS ***************************** --}}
    {{-- ***************************************************************** --}}
    <script>
        // Funciones de Modal Base
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

        // 1. Cliente: Rellenar y abrir modal de Edición (U)
        function openEditModal(cliente) {
            document.getElementById('editCedula').value = cliente.cedula;
            document.getElementById('editNombre').value = cliente.nombre;
            document.getElementById('editEmail').value = cliente.email;
            document.getElementById('editTelefono').value = cliente.telefono;
            document.getElementById('editDireccion').value = cliente.direccion;
            
            // Seleccionar la categoría actual del cliente (maneja null si no tiene)
            document.getElementById('editCategory').value = cliente.client_category_id || '';

            document.getElementById('editForm').action = `{{ url('clientes') }}/${cliente.id}`;
            openModal('editModal');
        }

        // 2. Cliente: Rellenar y abrir modal de Eliminación (D)
        function openDeleteModal(cliente) {
            document.getElementById('deleteClientName').innerText = cliente.nombre;
            document.getElementById('deleteForm').action = `{{ url('clientes') }}/${cliente.id}`;
            openModal('deleteModal');
        }
        
        // 3. Categoría: Rellenar y abrir modal de Edición de Categoría (U)
        function openEditCategoryModal(category) {
            document.getElementById('editCategoryName').value = category.name;
            document.getElementById('editCategoryColor').value = category.color_code;
            
            document.getElementById('editCategoryForm').action = `{{ url('categories') }}/${category.id}`;
            
            openModal('editCategoryModal');
        }

        // Manejar la reapertura de modales si hay errores (ej: si falla la validación de un formulario)
        @if ($errors->any())
            // Comprueba si el error viene de un formulario de categoría o cliente
            @php
                $isCategoryForm = old('name') && Request::routeIs('categories.store');
            @endphp
            
            @if ($isCategoryForm)
                openModal('manageCategoriesModal');
            @else
                // Reabre el modal de cliente (puede ser create o edit)
                openModal('{{ old('_method') === 'PUT' ? 'editModal' : 'createModal' }}');
                
                // Si falla la edición, rellenar los campos (Cédula ya viene en old('cedula'))
                @if (old('_method') === 'PUT')
                    document.getElementById('editCedula').value = '{{ old('cedula') }}';
                @endif
            @endif
        @endif
        
        // Cerrar modal al hacer click fuera del contenido (mejor experiencia de usuario)
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal')) {
                    closeModal(modal.id);
                }
            });
        });
    </script>
</x-app-layout>