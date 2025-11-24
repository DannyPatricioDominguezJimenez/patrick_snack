<!DOCTYPE html>
<html>
<head>
    <title>Nota de Venta #{{ $sale->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; margin: 0; padding: 0; }
        .invoice-box { 
            max-width: 800px; 
            margin: 20px auto; 
            /* === [AJUSTE CLAVE 1] REDUCIR O QUITAR EL PADDING SUPERIOR === */
            padding: 10px 20px 20px 20px; /* Reducido a 10px en la parte superior */
            border: 1px solid #eee; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); 
        }
        
        /* Estilos de la Nota de Venta... */
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            border-bottom: 2px solid #0d6efd; 
            padding-bottom: 10px; 
            /* === [AJUSTE CLAVE 2] QUITAR MARGEN SUPERIOR DEL HEADER === */
            margin-top: -10px; /* Neutraliza el padding superior de .invoice-box */
            margin-bottom: 20px; 
        }
        .logo-container { 
            width: 250px; 
            /* === [AJUSTE CLAVE 3] QUITAR MARGEN/PADDING SUPERIOR DEL CONTENEDOR DEL LOGO === */
            padding-top: 0;
            margin-top: 0;
        } 
        .logo { max-width: 100%; height: auto; }
        .invoice-info { 
            text-align: right; 
            line-height: 1.5; 
            padding-top: 0; 
        }
        .invoice-info h1 {
            margin-top: 0; /* Quitar margen superior del H1 */
            margin-bottom: 0;
        }
        
        /* ... el resto de estilos se mantienen ... */
        .client-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .details-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .details-table th { background-color: #f3f3f3; }
        .total-box { margin-top: 30px; border-top: 2px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        
        {{-- SECCIÓN DE ENCABEZADO Y LOGO --}}
        <div class="header">
            <div class="logo-container">
                @php
                    $path = public_path('images/logo.png'); 
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                @endphp
                <img src="{{ $base64 }}" alt="Logo de la Empresa" class="logo">
            </div>

            <div class="invoice-info">
                <h1 style="color: #0d6efd;">NOTA DE VENTA</h1> 
                <p style="margin-top: 5px;"><strong>N° Venta:</strong> {{ $sale->id }}</p>
                <p style="margin-top: 0;"><strong>Fecha de Emisión:</strong> {{ $sale->sale_date->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- ... el resto del cuerpo del HTML se mantiene igual ... --}}
        
        <div class="client-box">
            <h4>Información del Cliente</h4>
            <p style="margin: 3px 0;"><strong>Cliente:</strong> {{ $sale->client->nombre }}</p>
            <p style="margin: 3px 0;"><strong>Cédula/RUC:</strong> {{ $sale->client->cedula }}</p>
            <p style="margin: 3px 0;"><strong>Email:</strong> {{ $sale->client->email }}</p>
            <p style="margin: 3px 0;"><strong>Teléfono:</strong> {{ $sale->client->telefono }}</p>
        </div>

        <h3>Detalle de Productos Vendidos</h3>
        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 10%;">SKU</th>
                    <th style="width: 35%;">Producto</th>
                    <th style="width: 15%;">Gramaje</th>
                    <th style="width: 10%;">Cantidad</th>
                    <th style="width: 15%;">P. Unitario</th>
                    <th style="width: 15%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->details as $detail)
                    <tr>
                        <td>{{ $detail->product->sku }}</td>
                        <td>{{ $detail->product->name }}</td>
                        <td>{{ $detail->product->weight_grams ?? 'N/A' }} g</td>
                        <td>{{ $detail->quantity }}</td>
                        <td>${{ number_format($detail->unit_price, 2) }}</td>
                        <td>${{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <div style="width: 250px; float: right;">
                
                @php
                    $ivaRate = 0.15; // Tasa de IVA: 15%
                    $totalAmount = $sale->total_amount;
                    $subtotalSinIva = $totalAmount / (1 + $ivaRate); 
                    $montoIva = $totalAmount - $subtotalSinIva;
                @endphp

                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span style="font-weight: bold;">SUBTOTAL (sin IVA):</span>
                    <span>${{ number_format($subtotalSinIva, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span style="font-weight: bold;">IVA (15%):</span>
                    <span>${{ number_format($montoIva, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 10px; padding-top: 5px; border-top: 3px double #000; font-size: 1.3em;">
                    <span style="font-weight: bold;">TOTAL A PAGAR:</span>
                    <span style="color: #198754;">${{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div style="margin-top: 80px; padding-top: 20px;">
            <div style="display: flex; justify-content: space-between;">
                
                <div style="text-align: center; width: 40%;">
                    <br>
                    <div style="border-top: 1px solid #000; padding-top: 10px; margin-bottom: 5px;">
                        <span style="font-weight: bold;"><br>Patrick Snack (Administración)</span>
                    </div>
                    <span style="font-size: 0.8em; color: #555;">Recibido/Entregado por</span>
                </div>

                <div style="text-align: center; width: 40%; margin-left: auto;">
                    <div style="border-top: 1px solid #000; padding-top: 10px; margin-bottom: 5px;">
                        <span style="font-weight: bold;">{{ $sale->client->nombre ?? 'N/A' }}</span>
                    </div>
                    <span style="font-size: 0.8em; color: #555;">
                        Cliente (Cédula: {{ $sale->client->cedula ?? 'N/A' }})
                    </span>
                </div>
                
            </div>
            
            <div style="clear: both;"></div>
        </div>
        
    </div>
</body>
</html>