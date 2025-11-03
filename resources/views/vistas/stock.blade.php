<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #1f2937;">
            {{ __('Inventario y Control de Stock') }}
        </h2>
    </x-slot>

    {{-- ***************************************************************** --}}
    {{-- ******************* ESTILOS CSS (Diseño Uniforme) *************** --}}
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
        .btn-base { border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-primary { background-color: #0d6efd; color: white; box-shadow: 0 3px 8px rgba(13, 110, 253, 0.3); }
        .btn-primary:hover { background-color: #0b5ed7; transform: translateY(-1px); }
        .btn-clear { background-color: #6c757d; color: white; }
        .btn-clear:hover { background-color: #5a6268; }

        /* FILTROS */
        .filter-form { background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; border: 1px solid #e9ecef; }
        .filter-form input, .filter-form select { padding: 12px; border: 1px solid #ced4da; border-radius: 6px; width: 280px; }
        
        /* TABLA */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        .data-table th, .data-table td { padding: 16px; text-align: left; }
        .data-table thead th { background-color: #e9ecef; color: #343a40; font-weight: 700; border-bottom: 2px solid #dee2e6; text-transform: uppercase; font-size: 0.9em; }
        .data-table tbody tr { border-bottom: 1px solid #f1f1f1; transition: background-color 0.2s ease; }
        .data-table tbody tr:hover { background-color: #f8f8f8; }

        /* ETIQUETAS */
        .tag { display: inline-block; padding: 6px 14px; border-radius: 25px; font-size: 0.8em; font-weight: 700; }
        
        /* ESTADO DE STOCK */
        .status-danger { color: white; background-color: #dc3545; padding: 6px 10px; border-radius: 4px; }
        .status-low { color: #ffc107; font-weight: bold; }
        .status-ok { color: #198754; font-weight: bold; }
        .stock-input { padding: 8px; width: 100px; border: 1px solid #ccc; border-radius: 4px; text-align: center; }
        .stock-action-cell { width: 250px; }
        .stock-form { display: flex; gap: 5px; align-items: center; }
    </style>

    <div class="py-12">
        <div class="crud-container">
            <div class="card">
                <div style="padding: 10px;">

                    {{-- ENCABEZADO --}}
                    <div style="padding-bottom: 15px; border-bottom: 1px solid #eee; margin-bottom: 25px;">
                        <h3 style="font-size: 1.8em; font-weight: 700; color: #333;">Control de Cantidades y Existencias</h3>
                        <p style="color: #6c757d; margin-top: 5px;">Actualiza rápidamente el inventario de tus productos.</p>
                    </div>

                    {{-- FILTROS --}}
                    <form method="GET" action="{{ route('stock.index') }}" class="filter-form">
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar Nombre o SKU del producto...">
                        
                        {{-- FILTRO POR CATEGORÍA --}}
                        <select name="category_id">
                            <option value="">-- Filtrar por Categoría --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <button type="submit" class="btn-base btn-primary">Aplicar Filtros</button>
                        <a href="{{ route('stock.index') }}" class="btn-base btn-clear">Limpiar</a>
                    </form>

                    {{-- MENSAJES --}}
                    @if(session('success'))
                        <div style="background-color: #d4edda; color: #0f5132; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;">
                            ✅ {{ session('success') }}
                        </div>
                    @endif
                    
                    {{-- TABLA DE STOCK (READ & QUICK UPDATE) --}}
                    <div style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                    <th style="text-align: right;">Stock Actual</th>
                                    <th class="stock-action-cell" style="text-align: center;">Acción Rápida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    @php
                                        $currentQuantity = $product->stock->quantity ?? 0;
                                        $stockStatusClass = $currentQuantity == 0 ? 'status-danger' : ($currentQuantity < 10 ? 'status-low' : 'status-ok');
                                        $stockStatusText = $currentQuantity == 0 ? 'AGOTADO' : ($currentQuantity < 10 ? 'BAJO' : 'OK');
                                    @endphp
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td style="font-weight: 600;">{{ $product->sku }}</td>
                                        <td style="font-weight: 600;">{{ $product->name }}</td>
                                        <td>
                                            @if($product->category)
                                            <span class="tag" style="background-color: {{ $product->category->color_code ?? '#ccc' }}; color: {{ $product->category->color_code ? (strpos($product->category->color_code, '#000') !== false ? 'white' : 'black') : '#333' }};">
                                                {{ $product->category->name }}
                                            </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ $stockStatusClass }}">
                                                {{ $stockStatusText }}
                                            </span>
                                        </td>
                                        <td style="text-align: right; font-size: 1.2em;" class="{{ $stockStatusClass }}">
                                            {{ $currentQuantity }}
                                        </td>
                                        <td class="stock-action-cell">
                                            {{-- Formulario Rápido de Actualización (Inline) --}}
                                            <form action="{{ route('stock.update', $product) }}" method="POST" class="stock-form">
                                                @csrf @method('PUT')
                                                <input 
                                                    type="number" 
                                                    name="quantity" 
                                                    min="0" 
                                                    value="{{ $currentQuantity }}"
                                                    required 
                                                    class="stock-input"
                                                >
                                                <button type="submit" class="btn-base btn-primary" style="padding: 8px 15px; font-size: 0.9em;">
                                                    Guardar
                                                </button>
                                            </form>
                                            
                                            {{-- Mostrar errores de validación de Laravel si existen --}}
                                            @if ($errors->has('quantity') && old('product_id') == $product->id)
                                                <span style="color: #dc3545; font-size: 0.8em; display: block; margin-top: 5px;">
                                                    {{ $errors->first('quantity') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" style="text-align: center; color: #6c757d; padding: 30px; background-color: #fcfcfc;">No se encontraron productos que coincidan con los filtros.</td></tr>
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
</x-app-layout>