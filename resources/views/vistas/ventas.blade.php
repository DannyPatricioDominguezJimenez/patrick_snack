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
        /* BASE Y LAYOUT */
        .crud-container { max-width: 1250px; margin: 0 auto; padding: 20px; }
        .card { background-color: #fff; border-radius: 14px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1); padding: 35px; margin-top: 30px; border: 1px solid #e0e0e0; }
        
        /* BOTONES GENERALES */
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
        
        /* MODALES */
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); display: none; justify-content: center; align-items: center; z-index: 1000; opacity: 0; transition: opacity 0.3s ease; }
        .modal.active { opacity: 1; display: flex; }
        .modal-content { background-color: white; padding: 40px; border-radius: 12px; width: 90%; max-width: 900px; transform: scale(0.9); transition: transform 0.3s ease-out; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
        
        /* Modal de Eliminación */
        .modal-delete-content { max-width: 400px; text-align: center; padding: 30px; }
        .modal-delete-content h3 { color: #dc3545; margin-bottom: 10px; }
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

                <form method="GET" action="{{ route('ventas.index') }}" class="filter-form">
    {{-- 🚨 AJUSTE CLAVE: Usamos 6 columnas para mejor distribución horizontal --}}
    <div class="filter-grid" style="grid-template-columns: repeat(6, 1fr); gap: 15px; align-items: flex-end;"> 
        
        {{-- Grupo 1: Rango de Fechas (Ocupa 2 Columnas) --}}
        <div class="filter-item">
            <label for="start_date">Fecha Desde</label>
            <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}">
        </div>
        <div class="filter-item">
            <label for="end_date">Fecha Hasta</label>
            <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}">
        </div>

        {{-- Grupo 2: Estado y Pago (Ocupa 2 Columnas) --}}
        <div class="filter-item">
            <label for="status">Estado</label>
            <select name="status">
                <option value="">Todos los Estados</option>
                <option value="Pagada" {{ request('status') == 'Pagada' ? 'selected' : '' }}>Pagada</option>
                <option value="Pendiente" {{ request('status') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
            </select>
        </div>
        <div class="filter-item">
            <label for="payment_method">Método Pago</label>
            <select name="payment_method">
                <option value="">Todos los Métodos</option>
                <option value="Efectivo" {{ request('payment_method') == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                <option value="Transferencia" {{ request('payment_method') == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                <option value="Credito" {{ request('payment_method') == 'Credito' ? 'selected' : '' }}>Crédito</option>
            </select>
        </div>

        {{-- Grupo 3: Cliente (Individual y Categoría) (Ocupa 2 Columnas) --}}
        <div class="filter-item">
            <label for="client_id">Cliente Individual</label>
            <select name="client_id">
                <option value="">Todos los Clientes</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-item">
            <label for="client_category_id">Categoría Cliente</label>
            <select name="client_category_id">
                <option value="">Todas las Categorías</option>
                @foreach ($clientCategories as $cat)
                    <option value="{{ $cat->id }}" {{ request('client_category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 🚨 Filtro 4: Productos (Botón de Selección que ocupa 1 columna extra) --}}
        {{-- Movemos este botón fuera del grid principal y lo ponemos en la fila de acciones para mantener 6 columnas fijas --}}
        
    </div>
    
    {{-- ACCIONES DE FILTRO Y BOTÓN DE PRODUCTOS --}}
    <div class="filter-actions" style="grid-column: span 6; justify-content: space-between; padding-top: 10px;">
        
        <div style="width: 250px;">
             <button type="button" onclick="openProductFilterModal()" class="btn-base btn-cancel" style="width: 100%; padding: 10px;">
                Seleccionar Productos
            </button>
            @foreach ($products as $product)
                <input type="hidden" name="product_ids[]" id="hidden-prod-{{ $product->id }}" value="{{ in_array($product->id, request('product_ids', [])) ? $product->id : '' }}">
            @endforeach
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-base btn-primary" style="padding: 10px 30px;">
                🔍 Aplicar Filtros
            </button>
            <a href="{{ route('ventas.index') }}" class="btn-base btn-cancel" style="padding: 10px 30px;">
                Limpiar
            </a>
        </div>
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
                                <th>Método Pago</th> {{-- ⬅️ Nuevo Encabezado --}}
                                <th>Estado</th>
                                <th style="width: 280px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr>
                                    <td>{{ $sale->id }}</td>
                                    <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                    <td>{{ $sale->client->nombre }}</td>
                                    <td style="font-weight: 700; color: #198754;">${{ number_format($sale->total_amount, 2) }}</td>
                                    
                                    {{-- ⬅️ Nuevo Campo --}}
                                    <td>{{ $sale->payment_method }}</td>
                                    
                                    {{-- ⬅️ ComboBox de Estado Rápido --}}
                                    <td>
                                        <select onchange="updateSaleStatus({{ $sale->id }}, this.value)" 
                                                style="padding: 5px; border-radius: 6px; border: 1px solid #ccc; background-color: {{ $sale->status == 'Cancelada' ? '#f8d7da' : ($sale->status == 'Pendiente' ? '#fff3cd' : '#d4edda') }}; color: {{ $sale->status == 'Cancelada' ? '#721c24' : ($sale->status == 'Pendiente' ? '#664d03' : '#155724') }};">
                                            <option value="Pagada" {{ $sale->status == 'Pagada' ? 'selected' : '' }}>Pagada</option>
                                            <option value="Pendiente" {{ $sale->status == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="Cancelada" disabled>Cancelada</option>
                                        </select>
                                    </td>
                                    
                                    <td>
                                        {{-- Botones --}}
                                        <button onclick="openDetailsModal({{ $sale }})" class="btn-base" style="color: #0d6efd; padding: 8px;">📋 Detalle</button>
                                        <button onclick="openEditModal({{ $sale }})" class="btn-base" style="color: #0dcaf0; padding: 8px;">✏️ Editar</button>
                                        <button onclick="openDeleteConfirmModal({{ $sale }})" class="btn-base" style="color: #dc3545; padding: 8px;">🗑️ Cancelar</button>
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
                    {{-- Columna 1: Cliente --}}
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
                
                {{-- ⬅️ NUEVA FILA PARA MÉTODO Y ESTADO --}}
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label>Método de Pago</label>
                        <select name="payment_method" id="payment_method" required style="width: 100%; padding: 10px; border-radius: 6px;">
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Credito">Crédito</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label>Estado</label>
                        <select name="status" id="status" required style="width: 100%; padding: 10px; border-radius: 6px;">
                            <option value="Pagada">Pagada</option>
                            <option value="Pendiente">Pendiente</option>
                        </select>
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
            
            <div style="display: flex; gap: 30px; margin-bottom: 20px; border: 1px solid #eee; padding: 15px; border-radius: 8px;">
                {{-- Columna 1: Información de la Venta --}}
                <div style="flex: 1;">
                    <p>Fecha de Venta: <strong id="detailSaleDate"></strong></p>
                    <p>Estado: <strong id="detailSaleStatus"></strong></p>
                    <p>Método Pago: <strong id="detailPaymentMethod"></strong></p> {{-- ⬅️ Nuevo --}}
                </div>
                {{-- Columna 2: Información del Cliente --}}
                <div style="flex: 1;">
                    <p>Cliente: <strong id="detailClientName"></strong></p>
                    <p>Cédula: <strong id="detailClientCedula"></strong></p>
                    <p>Categoría: <strong id="detailClientCategory"></strong></p>
                </div>
            </div>

            <h4 style="font-size: 1.1em; font-weight: 700; margin-bottom: 10px; color: #333;">Productos del Pedido:</h4>
            
            <table class="details-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Cantidad Vendida</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="detailLinesBody">
                    {{-- Líneas de detalle inyectadas por JS --}}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold; padding-top: 15px;">TOTAL FINAL:</td>
                        <td id="detailTotalAmount" style="font-weight: bold; padding-top: 15px; font-size: 1.2em; color: #198754;"></td>
                    </tr>
                </tfoot>
            </table>

            <div class="modal-footer">
                <button type="button" onclick="closeModal('detailsModal')" class="btn-base btn-cancel">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- MODAL 3: FILTRO DE PRODUCTOS (Checkboxes) --}}
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
                            <label for="prod-{{ $product->id }}">{{ $product->name }} (SKU: {{ $product->sku }})</label>
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
    
    {{-- MODAL 4: CONFIRMACIÓN DE ELIMINACIÓN (CANCELAR VENTA) --}}
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content modal-delete-content">
            <h3 style="margin-bottom: 15px;">¿Cancelar Venta?</h3>
            <p>Estás a punto de cancelar la Venta <strong id="deleteSaleId"></strong> del cliente <strong id="deleteClientName"></strong>.</p>
            <p style="font-weight: 600; color: #dc3545;">¡El stock de los productos vendidos será devuelto al inventario!</p>
            
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                
                {{-- Inputs ocultos para mantener filtros y paginación después de la eliminación --}}
                <input type="hidden" name="start_date" value="{{ $startDate ?? '' }}">
                <input type="hidden" name="end_date" value="{{ $endDate ?? '' }}">
                <input type="hidden" name="page" value="{{ $sales->currentPage() }}">

                <div class="modal-footer" style="justify-content: space-around;">
                    <button type="button" onclick="closeModal('deleteConfirmModal')" class="btn-base btn-cancel">No, Mantener</button>
                    <button type="submit" class="btn-base btn-danger">Sí, Cancelar Venta</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ***************************************************************** --}}
    {{-- ************************ SCRIPTS JS (LÓGICA DINÁMICA) ************* --}}
    {{-- ***************************************************************** --}}
    <script>
        // DATA GLOBAL (JSON-encoded de PHP a JS)
        const ALL_PRODUCTS = @json($products);
        // 🚨 Cargar CLIENTES con categoría para el modal de detalle.
        const CLIENTS_WITH_DETAILS = @json($clientsWithCategory); 
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

        // Utilidad para formatear la fecha
        function formatDateDisplay(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            if (isNaN(date)) return dateString; 
            
            const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
            return date.toLocaleDateString('es-ES', options);
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

            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = parseFloat(selectedOption.getAttribute('data-price')) || 0.00;
                priceInput.value = price.toFixed(2);
                calculateLine(index, price);
            });

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
                // Usamos parseFloat aquí también, por seguridad
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
            
            document.getElementById('sale_date').value = new Date().toISOString().slice(0, 10);
            
            addProductLine();
            openModal('saleCrudModal');
        }

        // Abrir Modal de Edición
        function openEditModal(sale) {
            document.getElementById('saleModalTitle').textContent = `Editar Venta #${sale.id}`;
            document.getElementById('saleForm').setAttribute('action', `{{ url('ventas') }}/${sale.id}`);
            document.getElementById('saleModalMethod').value = 'PUT';

            // 🚨 Rellenar nuevos campos de pago y estado
            document.getElementById('payment_method').value = sale.payment_method;
            document.getElementById('status').value = sale.status;

            document.getElementById('client_id').value = sale.client_id;
            document.getElementById('sale_date').value = sale.sale_date.substring(0, 10);

            document.getElementById('detailsBody').innerHTML = '';
            sale.details.forEach(detail => {
                addProductLine(detail);
            });
            if (sale.details.length === 0) {
                 addProductLine();
            }
            
            openModal('saleCrudModal');
        }

        // Abrir Modal de Detalle (READ) ⬅️ FUNCIÓN CORREGIDA
        function openDetailsModal(sale) {
            document.getElementById('detailsModalTitle').textContent = `Detalles de la Venta #${sale.id}`;
            
            // 1. Obtener la data completa del cliente de la lista global
            const clientData = CLIENTS_WITH_DETAILS.find(c => c.id === sale.client_id);
            
            // 2. Acceso seguro a las propiedades
            const clientName = clientData ? clientData.nombre : 'N/A';
            const clientCedula = clientData ? clientData.cedula : 'N/A';
            const clientCategoryName = (clientData && clientData.category) ? clientData.category.name : 'N/A';
            
            // 3. Rellenar Información General
            document.getElementById('detailClientName').textContent = clientName;
            document.getElementById('detailSaleDate').textContent = formatDateDisplay(sale.sale_date);
            document.getElementById('detailClientCedula').textContent = clientCedula; 
            document.getElementById('detailClientCategory').textContent = clientCategoryName;
            document.getElementById('detailSaleStatus').textContent = sale.status;
            document.getElementById('detailPaymentMethod').textContent = sale.payment_method; // ⬅️ Nuevo campo

            // 4. Rellenar Líneas de Detalle (Productos)
            const linesBody = document.getElementById('detailLinesBody');
            linesBody.innerHTML = '';
            let total = 0; 
            
            sale.details.forEach(detail => {
                const product = detail.product;
                
                // 🚨 CONVERSIÓN CRÍTICA: Aseguramos que sea un número
                const unitPrice = parseFloat(detail.unit_price);
                const subtotal = parseFloat(detail.subtotal);
                total += subtotal;

                const row = linesBody.insertRow();
                row.innerHTML = `
                    <td>${product ? product.name : 'Producto Eliminado'}</td>
                    <td>${product ? product.sku : 'N/A'}</td>
                    <td>${detail.quantity}</td>
                    <td>$${unitPrice.toFixed(2)}</td>
                    <td>$${subtotal.toFixed(2)}</td>
                `;
            });

            document.getElementById('detailTotalAmount').textContent = '$' + total.toFixed(2); // Usamos el total local

            openModal('detailsModal');
        }

        // Abrir Modal de Eliminación 
        function openDeleteConfirmModal(sale) {
            document.getElementById('deleteSaleId').textContent = `#${sale.id}`;
            document.getElementById('deleteClientName').textContent = sale.client.nombre;
            
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.setAttribute('action', `{{ url('ventas') }}/${sale.id}`);

            openModal('deleteConfirmModal');
        }
        
        // Abrir Modal de Filtro de Productos
        function openProductFilterModal() {
            openModal('productFilterModal');
        }
        
        // --- MANEJO DE ERRORES DE LARAVEL ---

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
                            unit_price: productData.price 
                        };
                        addProductLine(restoredDetail);
                    } else {
                        addProductLine({product_id: detail.product_id, quantity: detail.quantity, unit_price: 0});
                    }
                });
            @else
                addProductLine();
            @endif
        @endif
    </script>
</x-app-layout>