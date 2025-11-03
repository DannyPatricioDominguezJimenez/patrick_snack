<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #1f2937;">
            {{ __('Gestión de Ventas y Pedidos') }}
        </h2>
    </x-slot>

    {{-- ***************************************************************** --}}
    {{-- ******************* ESTILOS CSS BASE Y MODAL ******************** --}}
    {{-- ***************************************************************** --}}
    <style>
        /* [ ... ESTILOS CSS GENERALES Y DE MODAL (IDÉNTICOS A LOS MÓDULOS ANTERIORES) ... ] */
        .crud-container { max-width: 1250px; margin: 0 auto; padding: 20px; }
        .card { background-color: #fff; border-radius: 14px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1); padding: 35px; margin-top: 30px; border: 1px solid #e0e0e0; }
        
        /* BOTONES Y FORMULARIOS */
        .btn-base { border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background-color: #0d6efd; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-success { background-color: #198754; color: white; }
        .btn-cancel { background-color: #f8f9fa; color: #333; border: 1px solid #ccc; }
        
        /* FILTROS */
        .filter-form { background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #e9ecef; }
        .filter-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; align-items: flex-end; }
        .filter-actions { grid-column: span 5; display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px; }
        .filter-item label { font-weight: 600; color: #343a40; display: block; margin-bottom: 5px; }
        .filter-item input, .filter-item select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }

        /* TABLA DE VENTAS */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .data-table th, .data-table td { padding: 16px; text-align: left; }
        .data-table thead th { background-color: #e9ecef; color: #343a40; font-weight: 700; text-transform: uppercase; font-size: 0.9em; }
        .data-table tbody tr:hover { background-color: #f8f8f8; }

        /* DETALLE DINÁMICO */
        .details-table th, .details-table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 0.9em; }
        .modal-content { max-width: 900px; } /* Modal más ancho para el carrito */
        
        /* MODALES */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: none; justify-content: center; align-items: center; z-index: 1000; opacity: 0; transition: opacity 0.3s ease; }
        .modal.active { opacity: 1; display: flex; }
        .modal-content { background-color: white; padding: 40px; border-radius: 12px; width: 90%; max-width: 900px; transform: scale(0.9); transition: transform 0.3s ease-out; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
    </style>

    <div class="py-12">
        <div class="crud-container">
            <div class="card">
                <h3 style="font-size: 1.8em; font-weight: 700; color: #333; margin-bottom: 20px;">
                    Listado de Ventas Realizadas
                </h3>
                
                {{-- MENSAJES --}}
                @if(session('success'))
                    <div style="background-color: #d4edda; color: #0f5132; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;">
                        ✅ {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="background-color: #f8d7da; color: #842029; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c2c7;">
                        ❌ {{ session('error') }}
                    </div>
                @endif

                {{-- FORMULARIO DE FILTROS COMPLETÍSIMO --}}
                <form method="GET" action="{{ route('ventas.index') }}" class="filter-form">
                    <div class="filter-grid">
                        
                        {{-- Filtro 1: Rango de Fechas --}}
                        <div class="filter-item">
                            <label for="start_date">Fecha Desde</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? now()->subDays(30)->toDateString() }}">
                        </div>
                        <div class="filter-item">
                            <label for="end_date">Fecha Hasta</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? now()->toDateString() }}">
                        </div>

                        {{-- Filtro 2: Cliente Individual --}}
                        <div class="filter-item">
                            <label for="client_id">Cliente</label>
                            <select name="client_id">
                                <option value="">Todos los Clientes</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Filtro 3: Categoría de Cliente --}}
                        <div class="filter-item">
                            <label for="client_category_id">Categoría de Cliente</label>
                            <select name="client_category_id">
                                <option value="">Todas las Categorías</option>
                                @foreach ($clientCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('client_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro 4: Productos (Checkboxes en un Desplegable) --}}
                        <div class="filter-item" style="grid-column: span 1;">
                            <label for="product_ids">Filtrar por Productos</label>
                            {{-- Simulamos un botón que abre un modal/desplegable para seleccionar --}}
                            <button type="button" onclick="openProductFilterModal()" class="btn-base btn-cancel" style="width: 100%; padding: 10px;">
                                Seleccionar Productos
                            </button>
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn-base btn-primary" style="padding: 10px 30px;">
                            🔍 Aplicar Filtros
                        </button>
                        <a href="{{ route('ventas.index') }}" class="btn-base btn-cancel" style="padding: 10px 30px;">
                            Limpiar
                        </a>
                    </div>
                </form>

                {{-- Botón de Creación de Venta --}}
                <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
                    <button onclick="openCreateModal()" class="btn-base btn-success">
                        + Registrar Nueva Venta
                    </button>
                </div>

                {{-- TABLA DE VENTAS (READ) --}}
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th style="width: 250px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr>
                                    <td>{{ $sale->id }}</td>
                                    <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                    <td>{{ $sale->client->nombre }}</td>
                                    <td style="font-weight: 700; color: #198754;">${{ number_format($sale->total_amount, 2) }}</td>
                                    <td>
                                        <span class="tag" style="background-color: {{ $sale->status == 'Cancelada' ? '#f8d7da' : '#d4edda' }}; color: {{ $sale->status == 'Cancelada' ? '#721c24' : '#155724' }};">
                                            {{ $sale->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="openDetailsModal({{ $sale }})" class="btn-base" style="color: #0d6efd; padding: 8px;">📋 Detalle</button>
                                        <button onclick="openEditModal({{ $sale }})" class="btn-base" style="color: #0dcaf0; padding: 8px;">✏️ Editar</button>
                                        
                                        <form action="{{ route('ventas.destroy', $sale) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar venta? Se reajustará el stock.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-base" style="color: #dc3545; padding: 8px;">🗑️ Cancelar</button>
                                        </form>
                                        
                                        <a href="{{ route('ventas.invoice', $sale) }}" target="_blank" class="btn-base" style="color: #343a40; padding: 8px;">📄 Nota</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align: center; color: #6c757d; padding: 30px;">No se encontraron ventas con los filtros aplicados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación --}}
                <div style="margin-top: 25px; display: flex; justify-content: flex-end;">
                    {{ $sales->appends(request()->query())->links() }} 
                </div>

            </div>
        </div>
    </div>

    {{-- ***************************************************************** --}}
    {{-- ******************** MODALES CRUD DE VENTAS *********************** --}}
    {{-- ***************************************************************** --}}
    
    {{-- MODAL 1: CREAR/EDITAR VENTA (Maestro-Detalle Dinámico) --}}
    <div id="saleCrudModal" class="modal">
        <div class="modal-content">
            <h3 id="saleModalTitle">Registrar Nueva Venta</h3>
            <form id="saleForm" method="POST" action="{{ route('ventas.store') }}">
                @csrf
                <input type="hidden" name="_method" id="saleModalMethod" value="POST">
                
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    {{-- Columna 1: Encabezado --}}
                    <div style="flex: 1;">
                        <label>Cliente</label>
                        <select name="client_id" id="client_id" required style="width: 100%; padding: 10px; border-radius: 6px;">
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Columna 2: Fecha --}}
                    <div style="flex: 1;">
                        <label>Fecha de Venta</label>
                        <input type="date" name="sale_date" id="sale_date" value="{{ now()->toDateString() }}" required style="width: 100%; padding: 10px; border-radius: 6px;">
                    </div>
                </div>
                
                <hr style="margin: 20px 0;">
                
                {{-- Área de Detalles (Carrito) --}}
                <h4 style="font-size: 1.2em; font-weight: 600; margin-bottom: 15px;">Detalles del Pedido</h4>
                
                <table id="detailsTable" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="padding: 10px;">Producto</th>
                            <th style="padding: 10px; width: 100px;">Precio Unitario</th>
                            <th style="padding: 10px; width: 80px;">Cantidad</th>
                            <th style="padding: 10px; width: 120px;">Subtotal</th>
                            <th style="padding: 10px; width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="detailsBody">
                        {{-- Filas de productos inyectadas por JS --}}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: bold; padding-top: 15px;">Total a Pagar:</td>
                            <td id="grandTotal" style="font-weight: bold; padding-top: 15px; font-size: 1.2em; color: #198754;">$0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                
                <button type="button" onclick="addProductLine()" class="btn-base" style="background-color: #ffc107; color: #343a40; padding: 8px 15px; margin-bottom: 20px;">+ Añadir Producto</button>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('saleCrudModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-success">Guardar Venta</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- MODAL 2: VISUALIZACIÓN DE DETALLES (READ) --}}
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <h3 id="detailsModalTitle">Detalles de la Venta #</h3>
            
            <p>Cliente: <strong id="detailClientName"></strong></p>
            <p>Fecha: <strong id="detailSaleDate"></strong></p>
            <hr style="margin: 15px 0;">

            <table class="details-table" style="width: 100%;">
                <thead>
                    <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
                </thead>
                <tbody id="detailLinesBody">
                    {{-- Líneas de detalle inyectadas por JS --}}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold; padding-top: 15px;">TOTAL:</td>
                        <td id="detailTotalAmount" style="font-weight: bold; padding-top: 15px; font-size: 1.2em; color: #198754;"></td>
                    </tr>
                </tfoot>
            </table>

            <div class="modal-footer">
                <button type="button" onclick="closeModal('detailsModal')" class="btn-base btn-cancel">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- MODAL 3: FILTRO DE PRODUCTOS (Para la vista Index) --}}
    <div id="productFilterModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <h3>Seleccionar Productos a Filtrar</h3>
            <form id="productFilterForm" method="GET" action="{{ route('ventas.index') }}">
                {{-- Preservar otros filtros --}}
                <input type="hidden" name="start_date" value="{{ $startDate ?? '' }}">
                <input type="hidden" name="end_date" value="{{ $endDate ?? '' }}">
                <input type="hidden" name="client_id" value="{{ request('client_id') ?? '' }}">
                <input type="hidden" name="client_category_id" value="{{ request('client_category_id') ?? '' }}">
                
                <div style="max-height: 300px; overflow-y: auto; border: 1px solid #eee; padding: 10px; margin-bottom: 20px;">
                    @foreach ($products as $product)
                        <div style="margin-bottom: 5px;">
                            <input type="checkbox" id="prod-{{ $product->id }}" name="product_ids[]" value="{{ $product->id }}" 
                                {{ in_array($product->id, request('product_ids', [])) ? 'checked' : '' }}>
                            <label for="prod-{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</label>
                        </div>
                    @endforeach
                </div>
                
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button type="button" onclick="closeModal('productFilterModal')" class="btn-base btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-base btn-primary">Aplicar Selección</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ***************************************************************** --}}
    {{-- ************************ SCRIPTS JS (LÓGICA DINÁMICA) ************* --}}
    {{-- ***************************************************************** --}}
    <script>
        const ALL_PRODUCTS = @json($products);
        const CLIENTS = @json($clients);
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
            if (id === 'saleCrudModal') {
                document.getElementById('detailsBody').innerHTML = ''; // Limpiar carrito
                productLineCounter = 0;
            }
        }

        // --- MANEJO DEL CARRITO (CREATE / UPDATE) ---

        function addProductLine(detail = null) {
            const body = document.getElementById('detailsBody');
            const row = body.insertRow();
            productLineCounter++;
            const index = productLineCounter;

            row.id = `row-${index}`;
            row.innerHTML = `
                <td style="padding-top: 10px;">
                    <select name="products[${index}][product_id]" class="product-select" data-index="${index}" required style="width: 100%; padding: 8px;">
                        <option value="">Seleccionar Producto</option>
                        ${ALL_PRODUCTS.map(p => 
                            `<option value="${p.id}" data-price="${p.price}" ${detail && detail.product_id === p.id ? 'selected' : ''}>${p.name} (SKU: ${p.sku})</option>`
                        ).join('')}
                    </select>
                </td>
                <td style="padding-top: 10px;">
                    <input type="number" step="0.01" class="unit-price" data-index="${index}" value="${detail ? detail.unit_price : '0.00'}" readonly style="width: 100%; padding: 8px; border: none; background: #f1f1f1;">
                </td>
                <td style="padding-top: 10px;">
                    <input type="number" name="products[${index}][quantity]" class="quantity-input" data-index="${index}" min="1" value="${detail ? detail.quantity : '1'}" required style="width: 100%; padding: 8px;">
                </td>
                <td class="subtotal-cell" data-index="${index}" style="font-weight: bold; padding-top: 10px;">
                    ${detail ? '$' + (detail.unit_price * detail.quantity).toFixed(2) : '$0.00'}
                </td>
                <td style="padding-top: 10px; text-align: center;">
                    <button type="button" onclick="removeProductLine(${index})" style="color: #dc3545; background: none; border: none; cursor: pointer;">&times;</button>
                </td>
            `;
            // Re-bind listeners for the new row
            setupLineListeners(index);
            calculateTotal();
        }

        function removeProductLine(index) {
            document.getElementById(`row-${index}`).remove();
            calculateTotal();
        }

        function setupLineListeners(index) {
            const row = document.getElementById(`row-${index}`);
            const select = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.unit-price');

            // Listener para el cambio de producto
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = parseFloat(selectedOption.getAttribute('data-price')) || 0.00;
                priceInput.value = price.toFixed(2);
                calculateLine(index, price);
            });

            // Listener para el cambio de cantidad
            quantityInput.addEventListener('input', function() {
                const price = parseFloat(priceInput.value);
                calculateLine(index, price);
            });
        }

        function calculateLine(index, price) {
            const row = document.getElementById(`row-${index}`);
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            const subtotal = price * quantity;
            row.querySelector('.subtotal-cell').textContent = '$' + subtotal.toFixed(2);
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-cell').forEach(cell => {
                const subtotalText = cell.textContent.replace('$', '');
                total += parseFloat(subtotalText) || 0;
            });
            document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
        }

        // --- LÓGICA DE MODALES ---

        // Abrir Modal de Creación
        function openCreateModal() {
            document.getElementById('saleModalTitle').textContent = 'Registrar Nueva Venta';
            document.getElementById('saleForm').setAttribute('action', '{{ route('ventas.store') }}');
            document.getElementById('saleModalMethod').value = 'POST';
            document.getElementById('detailsBody').innerHTML = '';
            addProductLine(); // Añadir una línea por defecto
            openModal('saleCrudModal');
        }

        // Abrir Modal de Edición
        function openEditModal(sale) {
            document.getElementById('saleModalTitle').textContent = `Editar Venta #${sale.id}`;
            document.getElementById('saleForm').setAttribute('action', `{{ url('ventas') }}/${sale.id}`);
            document.getElementById('saleModalMethod').value = 'PUT';

            // Rellenar encabezado
            document.getElementById('client_id').value = sale.client_id;
            document.getElementById('sale_date').value = sale.sale_date.substring(0, 10);

            // Limpiar y rellenar detalles
            document.getElementById('detailsBody').innerHTML = '';
            sale.details.forEach(detail => {
                addProductLine(detail);
            });
            if (sale.details.length === 0) {
                 addProductLine(); // Asegurar al menos una línea si no hay detalles
            }
            
            openModal('saleCrudModal');
        }

        // Abrir Modal de Detalle (READ)
        function openDetailsModal(sale) {
            document.getElementById('detailsModalTitle').textContent = `Detalles de la Venta #${sale.id}`;
            document.getElementById('detailClientName').textContent = sale.client.nombre;
            document.getElementById('detailSaleDate').textContent = sale.sale_date.substring(0, 10);
            
            const linesBody = document.getElementById('detailLinesBody');
            linesBody.innerHTML = '';
            
            sale.details.forEach(detail => {
                const product = ALL_PRODUCTS.find(p => p.id === detail.product_id);
                const row = linesBody.insertRow();
                row.innerHTML = `
                    <td>${product ? product.name : 'Producto Eliminado'}</td>
                    <td>${detail.quantity}</td>
                    <td>$${detail.unit_price.toFixed(2)}</td>
                    <td>$${detail.subtotal.toFixed(2)}</td>
                `;
            });

            document.getElementById('detailTotalAmount').textContent = '$' + sale.total_amount.toFixed(2);

            openModal('detailsModal');
        }

        // Abrir Modal de Filtro de Productos
        function openProductFilterModal() {
            openModal('productFilterModal');
        }
        
        // --- MANEJO DE ERRORES DE LARAVEL (PARA REAPERTURA DE MODAL) ---

        @if ($errors->any())
            openModal('saleCrudModal'); 
            
            // Si falla la validación, intenta restaurar las líneas del carrito
            @if (old('products'))
                document.getElementById('detailsBody').innerHTML = '';
                const oldDetails = @json(old('products'));
                Object.values(oldDetails).forEach(detail => {
                    const productData = ALL_PRODUCTS.find(p => p.id == detail.product_id);
                    if(productData) {
                        const restoredDetail = {
                            product_id: detail.product_id,
                            quantity: detail.quantity,
                            unit_price: productData.price // Usamos el precio actual del producto
                        };
                        addProductLine(restoredDetail);
                    } else {
                        // Si el producto no se encuentra (caso raro), añadir línea vacía
                        addProductLine({product_id: detail.product_id, quantity: detail.quantity, unit_price: 0});
                    }
                });
            @else
                addProductLine();
            @endif
        @endif
    </script>
</x-app-layout>