<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 1.8rem; font-weight: 700; color: #1f2937;">
            Vista Previa de Nota de Venta #{{ $sale->id }}
        </h2>
    </x-slot>

    <div style="padding: 20px; max-width: 1200px; margin: auto;">
        
        {{-- BOTÓN DE DESCARGA --}}
        <div style="margin-bottom: 20px; text-align: right;">
            <a href="{{ route('ventas.download', $sale) }}" class="btn-base" style="background-color: #198754; color: white; padding: 10px 25px; border-radius: 6px; text-decoration: none;">
                📥 Descargar PDF
            </a>
            <a href="{{ route('ventas.index') }}" class="btn-base" style="background-color: #6c757d; color: white; padding: 10px 25px; border-radius: 6px; text-decoration: none; margin-left: 10px;">
                Volver a Ventas
            </a>
        </div>

        {{-- VISUALIZADOR DE PDF --}}
        <div style="border: 1px solid #ccc; height: 80vh; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
            {{-- Usamos un iframe con la data Base64 para mostrar el PDF en el navegador --}}
            <iframe 
                src="data:application/pdf;base64,{{ $pdfBase64 }}" 
                style="width: 100%; height: 100%; border: none;"
            ></iframe>
        </div>
    </div>
</x-app-layout>