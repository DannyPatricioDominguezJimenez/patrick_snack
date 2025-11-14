<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #1f2937;">
            {{ __('Gestión de Productos') }}
        </h2>
    </x-slot>

    {{-- ***************************************************************** --}}
    {{-- ******************* ESTILOS CSS BASE Y MODAL ******************** --}}
    {{-- ***************************************************************** --}}
    <style>
        /* BASE Y LAYOUT */
        .crud-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background-color: #fff; border-radius: 14px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1); padding: 35px; margin-top: 30px; border: 1px solid #e0e0e0; }
        
        /* BOTONES GENERALES */
        .btn-base { border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; text-transform: uppercase; letter-spacing: 0.5px; }
        .btn-primary { background-color: #0d6efd; color: white; }
        .btn-secondary { background-color: #6f42c1; color: white; margin-left: 10px; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-clear { background-color: #6c757d; color: white; }

        /* FILTROS */
        .filter-form { background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #e9ecef; }
        .filter-form input, .filter-form select { padding: 12px; border: 1px solid #ced4da; border-radius: 6px; width: 280px; }

        /* TABLA */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .data-table th, .data-table td { padding: 16px; text-align: left; }
        .data-table thead th { background-color: #e9ecef; color: #343a40; font-weight: 700; text-transform: uppercase; font-size: 0.9em; }
        .data-table tbody tr:hover { background-color: #e6f7ff; }

        /* ETIQUETAS */
        .tag { display: inline-block; padding: 6px 14px; border-radius: 25px; font-size: 0.8em; font-weight: 700; }
        
        /* ACCIONES */
        .actions button { background: none; border: none; padding: 5px 8px; cursor: pointer; font-size: 1.2em; margin-right: 5px; }

        /* MODALES */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: none; justify-content: center; align-items: center; z-index: 1000; opacity: 0; transition: opacity 0.3s ease; }
        .modal.active { opacity: 1; display: flex; }
        .modal-content { background-color: white; padding: 40px; border-radius: 12px; width: 90%; max-width: 500px; transform: scale(0.9); transition: transform 0.3s ease-out; }
        .modal-content input, .modal-content select, .modal-content textarea { width: 100%; padding: 14px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 15px; box-sizing: border-box; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
        .category-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    </style>

    <div class="py-12">
        <div class="crud-container">
            <div class="card">
                <div style="padding: 10px;">

                    {{-- ENCABEZADO Y BOTONES DE ACCIÓN --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px; border-bottom: 1px solid #eee; margin-bottom: 25px;">
                        <h3 style="font-size: 1.8em; font-weight: 700; color: #333;">Inventario de Productos</h3>
                        <div>
                            <button onclick="openModal('manageCategoriesModal')" class="btn-base btn-secondary">
                                🏷️ Administrar Categorías
                            </button>
                            <button onclick="openModal('createModal')" class="btn-base btn-primary">
                                + Agregar Producto
                            </button>
                        </div>
                    </div>

                    {{-- FILTROS --}}
                    <form method="GET" action="{{ route('productos.index') }}" class="filter-form">
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar SKU, Nombre o Descripción...">
                        
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
                        <a href="{{ route('productos.index') }}" class="btn-base btn-clear" style="padding: 12px 20px;">Limpiar</a>
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

                    {{-- TABLA DE PRODUCTOS (R) --}}
                    <div style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SKU</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Peso (g)</th>  {{-- ⬅️ ¡NUEVA COLUMNA! --}}
                                    <th>Categoría</th> 
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td style="font-weight: 600;">{{ $product->sku }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td style="font-weight: 700; color: #198754;">${{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->weight_grams ?? 'N/A' }}g</td> {{-- ⬅️ ¡NUEVA CELDA! --}}
                                        <td>
                                            @if($product->category)
                                            <span class="tag" style="background-color: {{ $product->category->color_code ?? '#ccc' }}; color: {{ $product->category->color_code ? (strpos($product->category->color_code, '#000') !== false ? 'white' : 'black') : '#333' }};">
                                                {{ $product->category->name }}
                                            </span>
                                            @else
                                                <span class="tag" style="background-color: #f8d7da; color: #842029;">Sin Categoría</span>
                                            @endif
                                        </td>
                                        <td class="actions">
                                            <button onclick="openEditModal({{ $product }})" class="btn-edit" title="Editar">✏️</button>
                                            <button onclick="openDeleteModal({{ $product }})" class="btn-delete-icon" title="Eliminar">🗑️</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" style="text-align: center; color: #6c757d; padding: 30px; background-color: #fcfcfc;">No se encontraron productos con los filtros aplicados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div style="margin-top: 25px; display: flex; justify-content: flex-end;">
                        {{ $products->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ***************************************************************** --}}
    {{-- ******************* MODALES CRUD DE PRODUCTOS ******************* --}}
    {{-- ***************************************************************** --}}

    {{-- MODAL CREAR PRODUCTO (C) --}}
    <div id="createModal" class="modal">
        <div class="modal-content">
            <h3>Crear Nuevo Producto</h3>
            <form method="POST" action="{{ route('productos.store') }}">
                @csrf
                <input type="text" name="sku" placeholder="SKU del Producto (Referencia)" required value="{{ old('sku') }}">
                <input type="text" name="name" placeholder="Nombre del Producto" required value="{{ old('name') }}">
                <input type="number" name="price" placeholder="Precio (ej: 10.50)" step="0.01" required value="{{ old('price') }}">
                
                {{-- ⬅️ CAMPO PESO AGREGADO --}}
                <input type="number" name="weight_grams" placeholder="Peso en gramos (g)" min="0" value="{{ old('weight_grams') }}">
                
                {{-- SELECT CATEGORÍA --}}
                <select name="product_category_id">
                    <option value="">Seleccionar Categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                
                <textarea name="description" placeholder="Descripción (Opcional)"></textarea>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('createModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDITAR PRODUCTO (U) --}}
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Editar Producto</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="text" id="editSku" name="sku" placeholder="SKU del Producto (Referencia)" required>
                <input type="text" id="editName" name="name" placeholder="Nombre del Producto" required>
                <input type="number" id="editPrice" name="price" placeholder="Precio (ej: 10.50)" step="0.01" required>
                
                {{-- ⬅️ CAMPO PESO AGREGADO --}}
                <input type="number" id="editWeightGrams" name="weight_grams" placeholder="Peso en gramos (g)" min="0">
                
                {{-- SELECT CATEGORÍA --}}
                <select id="editCategory" name="product_category_id">
                    <option value="">Seleccionar Categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                
                <textarea id="editDescription" name="description" placeholder="Descripción (Opcional)"></textarea>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('editModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL ELIMINAR PRODUCTO (D) --}}
    <div id="deleteModal" class="modal">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <h3 style="margin-bottom: 10px; color: #dc3545;">¿Confirmar Eliminación?</h3>
            <p>Estás a punto de eliminar a: <strong id="deleteProductName" style="color: #000;"></strong>.</p>
            
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-footer" style="justify-content: space-between; margin-top: 30px;">
                    <button type="button" onclick="closeModal('deleteModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-danger">Sí, Eliminar Producto</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODALES CRUD DE CATEGORÍAS DE PRODUCTO (Se mantienen iguales) --}}
    
    {{-- MODAL PRINCIPAL: GESTIÓN DE CATEGORÍAS --}}
    <div id="manageCategoriesModal" class="modal">
        <div class="modal-content" style="max-width: 650px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3>Administrar Categorías de Producto</h3>
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
                                    
                                    <form action="{{ route('product_categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('ATENCIÓN: Eliminar categoría? Esto desvinculará a los productos asociados.');">
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

    {{-- SUB-MODAL: CREAR CATEGORÍA --}}
    <div id="createCategoryModal" class="modal">
        <div class="modal-content" style="max-width: 350px;">
            <h3>Crear Categoría de Producto</h3>
            <form method="POST" action="{{ route('product_categories.store') }}">
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

    {{-- SUB-MODAL: EDITAR CATEGORÍA --}}
    <div id="editCategoryModal" class="modal">
        <div class="modal-content" style="max-width: 350px;">
            <h3>Editar Categoría de Producto</h3>
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
        const ALL_PRODUCTS = @json($products);
        const CLIENTS = @json($clients); // Mantener por si se usa en otro lugar
        let productLineCounter = 0;

        // Base functions
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

        // 1. Abrir Modal de Edición (Rellenar todos los campos, incluido el peso)
        function openEditModal(product) {
            document.getElementById('editSku').value = product.sku;
            document.getElementById('editName').value = product.name;
            document.getElementById('editPrice').value = product.price;
            document.getElementById('editWeightGrams').value = product.weight_grams; // ⬅️ ¡RELLENAR PESO!
            document.getElementById('editDescription').value = product.description;
            
            // Seleccionar la categoría actual del producto
            document.getElementById('editCategory').value = product.product_category_id || '';

            document.getElementById('editForm').action = `{{ url('productos') }}/${product.id}`;
            openModal('editModal');
        }

        // 2. Abrir Modal de Eliminación (D)
        function openDeleteModal(product) {
            document.getElementById('deleteProductName').innerText = product.name;
            document.getElementById('deleteForm').action = `{{ url('productos') }}/${product.id}`;
            openModal('deleteModal');
        }
        
        // 3. Abrir Modal de Edición de Categoría
        function openEditCategoryModal(category) {
            document.getElementById('editCategoryName').value = category.name;
            document.getElementById('editCategoryColor').value = category.color_code;
            
            document.getElementById('editCategoryForm').action = `{{ url('product_categories') }}/${category.id}`;
            
            openModal('editCategoryModal');
        }

        // 4. Manejar la reapertura de modales si hay errores de validación
        @if ($errors->any())
            @if (session('open_modal'))
                openModal('{{ session('open_modal') }}');
            @else
                openModal('createModal'); 
            @endif
        @endif
        
        // Cerrar modal al hacer click fuera del contenido
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal')) {
                    closeModal(modal.id);
                }
            });
        });
    </script>
</x-app-layout>